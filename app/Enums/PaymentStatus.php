<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Unpaid = 'unpaid';
    case Partial = 'partial';

    /**
     * Work out the status of a month from money in vs. money due,
     * instead of asking the owner to pick it.
     */
    public static function fromAmounts(float $paid, float $due): self
    {
        return match (true) {
            $paid <= 0 => self::Unpaid,
            round($paid, 2) >= round($due, 2) => self::Paid,
            default => self::Partial,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Paid',
            self::Unpaid => 'Not paid',
            self::Partial => 'Part paid',
        };
    }
}
