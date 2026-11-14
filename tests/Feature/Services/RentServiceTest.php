<?php

namespace Tests\Feature\Services;

use App\Enums\LeaseStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use App\Services\RentService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RentServiceTest extends TestCase
{
    use RefreshDatabase;

    private CarbonImmutable $november;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->travelTo('2026-11-15 10:00:00');
        $this->november = CarbonImmutable::parse('2026-11-01');
    }

    private function lease(array $attributes = []): Lease
    {
        return Lease::factory()->create(array_merge([
            'rent_amount' => 10000,
            'service_charge' => null,
            'start_date' => '2026-01-01',
            'end_date' => null,
            'status' => LeaseStatus::Active,
        ], $attributes));
    }

    private function pay(Lease $lease, float $amount, string $period = '2026-11-01'): Payment
    {
        return Payment::factory()->create([
            'lease_id' => $lease->id,
            'amount_paid' => $amount,
            'rent_period' => $period,
        ]);
    }

    private function rent(): RentService
    {
        return app(RentService::class);
    }

    public function test_it_shows_who_has_paid_part_paid_and_not_paid(): void
    {
        $notPaid = $this->lease();
        $partPaid = $this->lease();
        $paid = $this->lease();

        $this->pay($partPaid, 4000);
        $this->pay($paid, 10000);

        $lines = $this->rent()->rollFor($this->november)->keyBy(fn ($line) => $line->lease->id);

        $this->assertSame(PaymentStatus::Unpaid, $lines[$notPaid->id]->status());
        $this->assertSame(10000.0, $lines[$notPaid->id]->balance());

        $this->assertSame(PaymentStatus::Partial, $lines[$partPaid->id]->status());
        $this->assertSame(6000.0, $lines[$partPaid->id]->balance());

        $this->assertSame(PaymentStatus::Paid, $lines[$paid->id]->status());
        $this->assertSame(0.0, $lines[$paid->id]->balance());
    }

    public function test_people_who_owe_are_listed_before_people_who_paid(): void
    {
        $paid = $this->lease();
        $this->pay($paid, 10000);
        $notPaid = $this->lease();

        $roll = $this->rent()->rollFor($this->november);

        $this->assertSame($notPaid->id, $roll->first()->lease->id);
        $this->assertSame($paid->id, $roll->last()->lease->id);
    }

    public function test_summary_totals(): void
    {
        $this->lease();
        $this->pay($this->lease(), 4000);
        $this->pay($this->lease(), 10000);

        $this->assertSame([
            'expected' => 30000.0,
            'collected' => 14000.0,
            'outstanding' => 16000.0,
            'due_count' => 2,
            'lease_count' => 3,
        ], $this->rent()->summaryFor($this->november));
    }

    public function test_an_overpayment_does_not_hide_what_others_owe(): void
    {
        $this->pay($this->lease(), 15000); // paid 5000 too much
        $this->lease();                    // owes 10000

        $summary = $this->rent()->summaryFor($this->november);

        $this->assertSame(10000.0, $summary['collected']);
        $this->assertSame(10000.0, $summary['outstanding']);
    }

    public function test_service_charge_is_part_of_the_rent_due(): void
    {
        $this->lease(['service_charge' => 1500]);

        $this->assertSame(11500.0, $this->rent()->summaryFor($this->november)['expected']);
    }

    public function test_only_leases_running_in_that_month_are_included(): void
    {
        $running = $this->lease();
        $endedMidMonth = $this->lease(['end_date' => '2026-11-10', 'status' => LeaseStatus::Terminated]);

        $this->lease(['start_date' => '2026-12-01']);                                       // not started yet
        $this->lease(['end_date' => '2026-10-31', 'status' => LeaseStatus::Expired]);      // ended before
        $this->lease(['status' => LeaseStatus::Terminated]);                               // ended, date unknown

        $ids = $this->rent()->rollFor($this->november)->map(fn ($line) => $line->lease->id)->sort()->values()->all();

        $this->assertSame([$running->id, $endedMidMonth->id], $ids);
    }

    public function test_payments_for_other_months_do_not_count(): void
    {
        $lease = $this->lease();
        $this->pay($lease, 10000, '2026-10-01');

        $this->assertSame(PaymentStatus::Unpaid, $this->rent()->lineFor($lease, $this->november)->status());
    }

    public function test_mark_paid_records_what_is_left_using_the_last_method(): void
    {
        $lease = $this->lease();
        $this->pay($lease, 4000)->update(['payment_method' => PaymentMethod::MobilePayment]);

        $payment = $this->rent()->markPaid($lease, $this->november);

        $this->assertEquals(6000, $payment->amount_paid);
        $this->assertSame(PaymentMethod::MobilePayment, $payment->payment_method);
        $this->assertSame('2026-11-01', $payment->rent_period->toDateString());
        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertTrue($this->rent()->lineFor($lease, $this->november)->isSettled());
    }

    public function test_mark_paid_twice_does_not_charge_twice(): void
    {
        $lease = $this->lease();

        $this->assertNotNull($this->rent()->markPaid($lease, $this->november));
        $this->assertNull($this->rent()->markPaid($lease, $this->november));
        $this->assertSame(1, Payment::count());
    }

    public function test_an_owner_only_sees_their_own_tenants(): void
    {
        $owner = User::factory()->create();
        $mine = Lease::factory()->ownedBy($owner)->create(['start_date' => '2026-01-01']);
        Lease::factory()->create(['start_date' => '2026-01-01']); // someone else's

        $this->actingAs($owner);

        $roll = $this->rent()->rollFor($this->november);

        $this->assertCount(1, $roll);
        $this->assertSame($mine->id, $roll->first()->lease->id);
    }
}
