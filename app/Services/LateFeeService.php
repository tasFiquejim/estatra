<?php

namespace App\Services;

use App\Models\Lease;
use Carbon\Carbon;

class LateFeeService
{
    public const PENALTY_RATE = 0.10;

    public const GRACE_PERIOD_DAYS = 4;

    /**
     * Calculate late fee for a lease based on payment date.
     * Rent is due on the 1st. 5-day grace period. After that, 10% late fee.
     */
    public function calculate(Lease $lease, Carbon $rentPeriod, Carbon $paymentDate): float
    {
        // 1. Find the 1st of the month for the rent period
        $dueDate = $rentPeriod->copy()->startOfMonth();

        // 2. Add the grace period
        $gracePeriodEnd = $dueDate->copy()->addDays(self::GRACE_PERIOD_DAYS);

        // 3. If paid after the grace period, charge the penalty rate
        if ($paymentDate->greaterThan($gracePeriodEnd)) {
            return $lease->rent_amount * self::PENALTY_RATE;
        }

        // 4. Otherwise, no late fee
        return 0.0;
    }
}
