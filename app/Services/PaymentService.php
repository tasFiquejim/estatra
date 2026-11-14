<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Mail\PaymentReceiptMail;
use App\Models\Lease;
use App\Models\Payment;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['receipt_number'])) {
                $data['receipt_number'] = $this->generateReceiptNo();
            }

            $data['rent_period'] = $this->normalizePeriod($data['rent_period']);

            $payment = Payment::create($data);

            $this->syncPeriodStatus($payment->lease_id, $payment->rent_period);

            $payment->refresh()->load('lease.tenant');
            $tenant = $payment->lease?->tenant;

            if ($tenant && $tenant->email) {
                Mail::to($tenant->email)->send(new PaymentReceiptMail($payment));
            }

            return $payment;
        });
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $oldLeaseId = $payment->lease_id;
            $oldPeriod = $payment->rent_period->copy();

            if (isset($data['rent_period'])) {
                $data['rent_period'] = $this->normalizePeriod($data['rent_period']);
            }

            $payment->update($data);

            // Re-check both the month it was in and the month it is in now.
            $this->syncPeriodStatus($oldLeaseId, $oldPeriod);
            $this->syncPeriodStatus($payment->lease_id, $payment->rent_period);

            return $payment->refresh();
        });
    }

    /**
     * Set the status of every payment for one lease + month,
     * based on the total paid for that month vs. the monthly charge.
     */
    public function syncPeriodStatus(?int $leaseId, CarbonInterface $period): void
    {
        $lease = $leaseId ? Lease::find($leaseId) : null;

        if ($lease === null) {
            return;
        }

        $paymentsForMonth = Payment::query()
            ->where('lease_id', $lease->id)
            ->whereYear('rent_period', $period->year)
            ->whereMonth('rent_period', $period->month);

        $paid = (float) (clone $paymentsForMonth)->sum('amount_paid');

        $paymentsForMonth->update([
            'status' => PaymentStatus::fromAmounts($paid, $lease->monthlyCharge()),
        ]);
    }

    private function normalizePeriod(mixed $period): string
    {
        return Carbon::parse($period)->startOfMonth()->toDateString();
    }

    private function generateReceiptNo(): string
    {
        // Receipt numbers are unique across ALL users, so look past the owner scope.
        $lastNumber = Payment::withoutGlobalScope('owner')
            ->whereNotNull('receipt_number')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastNumber ? ((int) $lastNumber->receipt_number) + 1 : 1;

        return str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
