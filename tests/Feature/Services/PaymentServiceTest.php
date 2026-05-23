<?php

namespace Tests\Feature\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Mail\PaymentReceiptMail;
use App\Models\Lease;
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
        $service = new PaymentService();

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

        $service = new PaymentService();
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
}
