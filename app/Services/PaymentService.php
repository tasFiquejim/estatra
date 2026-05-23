<?php

namespace App\Services;

use App\Mail\PaymentReceiptMail;
use App\Models\Payment;
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

            $payment = Payment::create($data);
            
            $payment->load('lease.tenant');
            $tenant = $payment->lease->tenant;
            
            if ($tenant && $tenant->email) {
                Mail::to($tenant->email)->send(new PaymentReceiptMail($payment));
            }

            \App\Jobs\GenerateReceiptPdfJob::dispatch($payment);

            return $payment;
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
