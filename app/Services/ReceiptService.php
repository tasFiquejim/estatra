<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReceiptService
{
    /**
     * Generate a PDF receipt for the given payment and return it
     * as an inline response (opens in browser) or as a download.
     *
     * We load all the nested relationships here so the Blade view
     * stays clean and doesn't trigger lazy-load queries.
     */
    public function generateReceiptPdf(Payment $payment): Response
    {
        $payment->loadMissing([
            'lease.tenant',
            'lease.unit.property',
        ]);

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
        ])->setPaper('a4', 'portrait');

        $filename = 'receipt-' . $payment->receipt_number . '.pdf';

        return $pdf->download($filename);
    }
}
