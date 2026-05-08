<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\Gate;

class ReceiptController extends Controller
{
    public function download(Payment $payment, ReceiptService $receiptService)
    {
        // Authorization: only the owner of the property can download receipts
        Gate::authorize('view', $payment);

        return $receiptService->generateReceiptPdf($payment);
    }
}
