<?php

namespace App\Services;

use App\Data\RentLine;
use App\Enums\LeaseStatus;
use App\Enums\PaymentMethod;
use App\Models\Lease;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Answers the owner's main question: "who has paid this month, and who still owes me?"
 *
 * Nothing new is stored. Rent due is worked out from the leases:
 *   expected = rent + service charge, for every lease running that month
 *   paid     = sum of payments recorded for that lease + month
 */
class RentService
{
    public function __construct(private PaymentService $payments) {}

    /**
     * Every lease that was running in the given month, with what it owes.
     * Unpaid and part-paid come first, then paid; each group sorted by property and unit.
     *
     * @return Collection<int, RentLine>
     */
    public function rollFor(CarbonInterface $month): Collection
    {
        return $this->leasesRunningIn($month)
            ->with(['unit.property', 'tenant'])
            ->withSum(['payments as paid_total' => fn (Builder $q) => $this->inMonth($q, $month)], 'amount_paid')
            ->get()
            ->map(fn (Lease $lease) => new RentLine(
                lease: $lease,
                expected: $lease->monthlyCharge(),
                // paid_total only exists because of the withSum() above
                paid: round((float) $lease->getAttribute('paid_total'), 2),
            ))
            ->sortBy([
                fn (RentLine $a, RentLine $b) => $a->isSettled() <=> $b->isSettled(),
                fn (RentLine $a, RentLine $b) => strnatcasecmp(
                    $a->lease->unit->property->property_name.' '.$a->lease->unit->unit_name,
                    $b->lease->unit->property->property_name.' '.$b->lease->unit->unit_name,
                ),
            ])
            ->values();
    }

    /**
     * Totals for the dashboard.
     *
     * @return array{expected: float, collected: float, outstanding: float, due_count: int, lease_count: int}
     */
    public function summaryFor(CarbonInterface $month): array
    {
        $roll = $this->rollFor($month);

        return [
            'expected' => round($roll->sum('expected'), 2),
            // Count at most what was due, so an overpayment doesn't hide someone else's debt.
            'collected' => round($roll->sum(fn (RentLine $line) => min($line->paid, $line->expected)), 2),
            'outstanding' => round($roll->sum(fn (RentLine $line) => $line->balance()), 2),
            'due_count' => $roll->reject(fn (RentLine $line) => $line->isSettled())->count(),
            'lease_count' => $roll->count(),
        ];
    }

    public function lineFor(Lease $lease, CarbonInterface $month): RentLine
    {
        $paid = (float) $this->inMonth(Payment::query()->where('lease_id', $lease->id), $month)
            ->sum('amount_paid');

        return new RentLine($lease, $lease->monthlyCharge(), round($paid, 2));
    }

    /**
     * Months this lease still owes rent for, up to and including $until
     * (or the lease's end, if earlier).
     *
     * Counting starts at the lease start, or at the first month with a recorded
     * payment if that is later: owners often start using Estatra part-way
     * through a lease, and the months before were settled outside the app.
     *
     * @return Collection<int, RentLine>
     */
    public function unpaidMonths(Lease $lease, CarbonInterface $until): Collection
    {
        $paidByMonth = Payment::query()
            ->where('lease_id', $lease->id)
            ->get(['rent_period', 'amount_paid'])
            ->groupBy(fn (Payment $payment) => $payment->rent_period->format('Y-m'))
            ->map(fn (Collection $payments) => round((float) $payments->sum('amount_paid'), 2));

        $from = CarbonImmutable::parse($lease->start_date)->startOfMonth();
        $firstPaid = $paidByMonth->keys()->sort()->first();
        if ($firstPaid !== null && ($firstPaidMonth = CarbonImmutable::parse($firstPaid.'-01'))->gt($from)) {
            $from = $firstPaidMonth;
        }

        $to = CarbonImmutable::parse($until)->startOfMonth();
        if ($lease->end_date !== null && $lease->end_date->lt($to)) {
            $to = CarbonImmutable::parse($lease->end_date)->startOfMonth();
        }

        $unpaid = collect();
        for ($month = $from; $month->lte($to); $month = $month->addMonth()) {
            $line = new RentLine($lease, $lease->monthlyCharge(), $paidByMonth[$month->format('Y-m')] ?? 0.0, $month);

            if (! $line->isSettled()) {
                $unpaid->push($line);
            }
        }

        return $unpaid;
    }

    /**
     * The payment method this tenant used last time, so the owner doesn't have to pick it again.
     */
    public function lastMethodFor(Lease $lease): PaymentMethod
    {
        return Payment::query()
            ->where('lease_id', $lease->id)
            ->latest('payment_date')
            ->latest('id')
            ->value('payment_method') ?? PaymentMethod::Cash;
    }

    /**
     * Record rent received for a month (full or part payment).
     */
    public function recordRent(
        Lease $lease,
        CarbonInterface $month,
        float $amount,
        PaymentMethod $method,
        CarbonInterface $paidOn,
    ): Payment {
        return $this->payments->recordPayment([
            'lease_id' => $lease->id,
            'amount_paid' => round($amount, 2),
            'payment_date' => $paidOn->toDateString(),
            'rent_period' => $month->copy()->startOfMonth()->toDateString(),
            'payment_method' => $method,
        ]);
    }

    /**
     * Record whatever is still owed for the month, paid today by the usual method.
     * Returns null when nothing is owed, so a double click can't pay twice.
     */
    public function markPaid(Lease $lease, CarbonInterface $month): ?Payment
    {
        $balance = $this->lineFor($lease, $month)->balance();

        if ($balance <= 0) {
            return null;
        }

        return $this->recordRent($lease, $month, $balance, $this->lastMethodFor($lease), now());
    }

    /**
     * A lease counts for a month if it started on or before the month's last day
     * and had not ended before the month's first day. A lease that is no longer
     * active only counts if it has an end date (otherwise we can't tell when it ended).
     *
     * @return Builder<Lease>
     */
    private function leasesRunningIn(CarbonInterface $month): Builder
    {
        $first = $month->copy()->startOfMonth()->toDateString();
        $last = $month->copy()->endOfMonth()->toDateString();

        return Lease::query()
            ->whereDate('start_date', '<=', $last)
            ->where(fn (Builder $q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', $first))
            ->where(fn (Builder $q) => $q->where('status', LeaseStatus::Active)->orWhereNotNull('end_date'));
    }

    /**
     * @param  Builder<Payment>  $query
     * @return Builder<Payment>
     */
    private function inMonth(Builder $query, CarbonInterface $month): Builder
    {
        return $query
            ->whereYear('rent_period', $month->year)
            ->whereMonth('rent_period', $month->month);
    }
}
