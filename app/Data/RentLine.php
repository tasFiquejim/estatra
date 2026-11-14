<?php

namespace App\Data;

use App\Enums\PaymentStatus;
use App\Models\Lease;
use Carbon\CarbonImmutable;

/**
 * One row of the rent roll: what one lease owes for one month.
 */
final readonly class RentLine
{
    public function __construct(
        public Lease $lease,
        public float $expected,
        public float $paid,
        /** The month this line is for, when it isn't obvious from context. */
        public ?CarbonImmutable $month = null,
    ) {}

    public function balance(): float
    {
        return max(0.0, round($this->expected - $this->paid, 2));
    }

    public function status(): PaymentStatus
    {
        return PaymentStatus::fromAmounts($this->paid, $this->expected);
    }

    public function isSettled(): bool
    {
        return $this->status() === PaymentStatus::Paid;
    }
}
