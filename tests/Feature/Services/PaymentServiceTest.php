<?php

namespace Tests\Feature\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Mail\PaymentReceiptMail;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_a_payment_and_auto_generates_receipt_number(): void
    {
        // 1. Arrange (Set up the world)
        $lease = Lease::factory()->create();
        $service = new PaymentService;

        // Prepare the data array. Note: we are purposefully omitting 'receipt_number'
        $paymentData = [
            'lease_id' => $lease->id,
            'amount_paid' => 1500.00,
            'payment_date' => now()->format('Y-m-d'),
            'rent_period' => now()->startOfMonth()->format('Y-m'),
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => PaymentStatus::Paid,
            'notes' => 'First rent payment',
        ];

        // 2. Act (Perform the action)
        $payment = $service->recordPayment($paymentData);

        // 3. Assert (Verify the outcome)
        $this->assertNotNull($payment->id);
        $this->assertEquals('0001', $payment->receipt_number);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'lease_id' => $lease->id,
            'amount_paid' => 1500.00,
            'receipt_number' => '0001',
        ]);
    }

    public function test_it_dispatches_an_email_receipt_when_payment_is_recorded(): void
    {
        // 1. Arrange: Intercept all outgoing emails
        Mail::fake();

        $service = new PaymentService;
        $lease = Lease::factory()->create();

        $paymentData = [
            'lease_id' => $lease->id,
            'amount_paid' => 1500.00,
            'payment_date' => now()->format('Y-m-d'),
            'rent_period' => now()->startOfMonth()->format('Y-m-d'),
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => PaymentStatus::Paid,
        ];

        // 2. Act
        $service->recordPayment($paymentData);

        // 3. Assert: Prove the exact email was queued to the exact tenant
        Mail::assertSent(PaymentReceiptMail::class, function ($mail) use ($lease) {
            return $mail->hasTo($lease->tenant->email);
        });
    }

    public function test_a_month_can_be_paid_in_parts_and_status_is_worked_out(): void
    {
        Mail::fake();
        $service = new PaymentService;
        $lease = Lease::factory()->create(['rent_amount' => 10000, 'service_charge' => 500]);

        $data = [
            'lease_id' => $lease->id,
            'payment_date' => now()->format('Y-m-d'),
            'rent_period' => now()->format('Y-m'),
            'payment_method' => PaymentMethod::Cash,
        ];

        $first = $service->recordPayment([...$data, 'amount_paid' => 6000]);
        $this->assertSame(PaymentStatus::Partial, $first->status);

        $second = $service->recordPayment([...$data, 'amount_paid' => 4500]);

        // Both payments for the month now show Paid: 6000 + 4500 = 10000 rent + 500 service charge
        $this->assertSame(PaymentStatus::Paid, $second->status);
        $this->assertSame(PaymentStatus::Paid, $first->fresh()->status);
    }

    public function test_moving_a_payment_to_another_month_updates_both_months(): void
    {
        Mail::fake();
        $service = new PaymentService;
        $lease = Lease::factory()->create(['rent_amount' => 10000, 'service_charge' => null]);

        $base = [
            'lease_id' => $lease->id,
            'payment_date' => '2026-01-05',
            'payment_method' => PaymentMethod::Cash,
        ];
        $partOfJanuary = $service->recordPayment([...$base, 'amount_paid' => 4000, 'rent_period' => '2026-01-01']);
        $restOfJanuary = $service->recordPayment([...$base, 'amount_paid' => 6000, 'rent_period' => '2026-01-01']);
        $this->assertSame(PaymentStatus::Paid, $partOfJanuary->fresh()->status);

        // Owner fixes a mistake: the 6000 was really for February.
        $service->updatePayment($restOfJanuary, ['rent_period' => '2026-02-01']);

        $this->assertSame(PaymentStatus::Partial, $partOfJanuary->fresh()->status);
        $this->assertSame(PaymentStatus::Partial, $restOfJanuary->fresh()->status);
    }

    public function test_receipt_numbers_do_not_clash_between_owners(): void
    {
        Mail::fake();
        $service = new PaymentService;

        foreach ([User::factory()->create(), User::factory()->create()] as $owner) {
            $lease = Lease::factory()->ownedBy($owner)->create();
            $this->actingAs($owner);

            $service->recordPayment([
                'lease_id' => $lease->id,
                'amount_paid' => 1000,
                'payment_date' => now()->format('Y-m-d'),
                'rent_period' => now()->format('Y-m'),
                'payment_method' => PaymentMethod::Cash,
            ]);
        }

        // Before the fix, the second owner only saw their own (zero) payments,
        // generated "0001" again and hit the unique index.
        $this->assertSame(['0001', '0002'], Payment::withoutGlobalScopes()->orderBy('id')->pluck('receipt_number')->all());
    }
}
