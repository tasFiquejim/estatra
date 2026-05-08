<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    private static int $receiptCounter = 1;

    public function definition(): array
    {
        $receiptNumber = str_pad(self::$receiptCounter++, 4, '0', STR_PAD_LEFT);

        return [
            'lease_id'       => Lease::factory(),
            'receipt_number' => $receiptNumber,
            'payment_date'   => $this->faker->dateTimeBetween('-3 months', 'now'),
            'rent_period'    => now()->startOfMonth(),
            'amount_paid'    => 10000,
            'payment_method' => $this->faker->randomElement(PaymentMethod::cases()),
            'status'         => PaymentStatus::Paid,
            'notes'          => $this->faker->optional(0.3)->sentence(5),
        ];
    }

    public function paid(): static
    {
        return $this->state(['status' => PaymentStatus::Paid]);
    }

    public function partial(): static
    {
        return $this->state(['status' => PaymentStatus::Partial]);
    }
}
