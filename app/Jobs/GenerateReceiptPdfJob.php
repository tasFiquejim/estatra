<?php

namespace App\Jobs;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateReceiptPdfJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment
    ) {}

    public function handle(): void
    {
        $this->payment->loadMissing([
            'lease.tenant',
            'lease.unit.property',
        ]);

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $this->payment,
        ])->setPaper('a4', 'portrait');

        // Save it directly to the 'receipts' folder on the 'public' disk
        $filename = 'receipts/receipt-'.$this->payment->receipt_number.'.pdf';

        Storage::disk('public')->put($filename, $pdf->output());
    }
}
