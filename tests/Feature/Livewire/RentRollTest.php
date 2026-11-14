<?php

namespace Tests\Feature\Livewire;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Livewire\V1\Payment\PaymentForm;
use App\Livewire\V1\Rent\RentRoll;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RentRollTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Lease $lease;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->travelTo('2026-11-15 10:00:00');

        $this->owner = User::factory()->create();
        $this->lease = Lease::factory()->ownedBy($this->owner)->create([
            'rent_amount' => 12000,
            'service_charge' => null,
            'start_date' => '2026-01-01',
        ]);
    }

    public function test_the_rent_page_and_dashboard_load(): void
    {
        $this->actingAs($this->owner)->get(route('rent.index'))
            ->assertOk()
            ->assertSeeLivewire(RentRoll::class)
            ->assertSee($this->lease->tenant->first_name)
            ->assertSee('Not paid');

        $this->actingAs($this->owner)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rent for November')
            ->assertSee('1 tenant still owes');
    }

    public function test_mark_paid_opens_a_prefilled_popup(): void
    {
        Payment::factory()->create([
            'lease_id' => $this->lease->id,
            'amount_paid' => 2000,
            'rent_period' => '2026-10-01',
            'payment_date' => '2026-10-03',
            'payment_method' => PaymentMethod::MobilePayment,
        ]);

        Livewire::actingAs($this->owner)
            ->test(RentRoll::class)
            ->assertDontSee('Record rent')
            ->call('openPayment', $this->lease->id)
            ->assertSee('Record rent')
            ->assertSet('payAmount', 12000.0)
            ->assertSet('payMethod', 'mobile_payment')   // what they used last time
            ->assertSet('payDate', '2026-11-15')
            ->call('closePayment')
            ->assertDontSee('Record rent');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_confirming_the_popup_records_the_rent(): void
    {
        Livewire::actingAs($this->owner)
            ->test(RentRoll::class)
            ->call('openPayment', $this->lease->id)
            ->call('confirmPayment')
            ->assertHasNoErrors()
            ->assertSet('payingLeaseId', null)
            ->assertSet('notice', fn ($notice) => str_contains($notice, '৳12,000.00'))
            ->assertSee('Everyone has paid');

        $this->assertDatabaseHas('payments', ['lease_id' => $this->lease->id, 'amount_paid' => 12000]);
    }

    public function test_the_owner_can_change_the_amount_for_a_part_payment(): void
    {
        Livewire::actingAs($this->owner)
            ->test(RentRoll::class)
            ->call('openPayment', $this->lease->id)
            ->set('payAmount', 5000)
            ->assertSee('7,000.00 will still be owed')
            ->set('payMethod', 'cash')
            ->set('payDate', '2026-11-14')
            ->call('confirmPayment')
            ->assertHasNoErrors()
            ->assertSee('Part paid');

        $payment = Payment::sole();
        $this->assertEquals(5000, $payment->amount_paid);
        $this->assertSame('2026-11-14', $payment->payment_date->toDateString());
        $this->assertSame(PaymentStatus::Partial, $payment->status);
    }

    public function test_the_popup_stays_open_with_errors_for_bad_input(): void
    {
        Livewire::actingAs($this->owner)
            ->test(RentRoll::class)
            ->call('openPayment', $this->lease->id)
            ->set('payAmount', 0)
            ->set('payDate', '2026-12-01')
            ->set('payMethod', 'gold-coins')
            ->call('confirmPayment')
            ->assertHasErrors(['payAmount', 'payDate', 'payMethod'])
            ->assertSet('payingLeaseId', $this->lease->id)
            ->assertSee('Record rent');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_an_owner_cannot_open_the_popup_for_someone_elses_lease(): void
    {
        $otherLease = Lease::factory()->create(['start_date' => '2026-01-01']);

        Livewire::actingAs($this->owner)
            ->test(RentRoll::class)
            ->call('openPayment', $otherLease->id)
            ->assertNotFound()
            ->assertSet('payingLeaseId', null);
    }

    public function test_month_navigation_stops_one_month_ahead(): void
    {
        Livewire::actingAs($this->owner)
            ->test(RentRoll::class)
            ->assertSet('month', '2026-11')
            ->call('previousMonth')
            ->assertSet('month', '2026-10')
            ->call('nextMonth')
            ->call('nextMonth')
            ->assertSet('month', '2026-12')
            ->call('nextMonth')
            ->assertSet('month', '2026-12');
    }

    public function test_a_bad_month_in_the_url_falls_back_to_this_month(): void
    {
        Livewire::withQueryParams(['month' => '2026-13'])
            ->actingAs($this->owner)
            ->test(RentRoll::class)
            ->assertSet('month', '2026-11');
    }

    public function test_other_amount_link_prefills_the_payment_form(): void
    {
        Payment::factory()->create([
            'lease_id' => $this->lease->id,
            'amount_paid' => 5000,
            'rent_period' => '2026-11-01',
        ]);

        $this->actingAs($this->owner)
            ->get(route('payment.create', ['lease' => $this->lease->id, 'period' => '2026-11']))
            ->assertOk()
            ->assertSeeLivewire('v1.payment.payment-form');

        Livewire::withQueryParams(['lease' => $this->lease->id, 'period' => '2026-11'])
            ->actingAs($this->owner)
            ->test(PaymentForm::class)
            ->assertSet('form.lease_id', $this->lease->id)
            ->assertSet('form.rent_period', '2026-11')
            ->assertSet('form.amount_paid', 7000.0);
    }
}
