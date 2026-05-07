<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['receipt_number'])) {
                $data['receipt_number'] = $this->generateReceiptNo();
            }

            return Payment::create($data);
        });
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $payment->update($data);
            return $payment;
        });
    }

    private function generateReceiptNo(): string
    {
        $lastNumber = Payment::whereNotNull('receipt_number')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastNumber ? ((int)$lastNumber->receipt_number) + 1 : 1;

        return str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
