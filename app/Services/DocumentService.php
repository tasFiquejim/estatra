<?php

namespace App\Services;

use App\Enums\LeaseStatus;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\PropertyExpense;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class DocumentService
{
    /**
     * Generate a PDF receipt for a specific payment.
     */
    public function generatePaymentReceipt(Payment $payment)
    {
        // Security check: Ensure user owns this payment data
        Gate::authorize('view', $payment);

        $data = [
            'payment' => $payment->load(['lease.tenant', 'lease.unit.property'])
        ];

        return Pdf::loadView('pdf.receipt', $data);
    }

    /**
     * Generate a monthly owner statement.
     */
    public function generateOwnerStatement(int $month, int $year)
    {
        $payments = Payment::with(['lease.tenant', 'lease.unit.property'])
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->get();

        $expenses = PropertyExpense::with('property')
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->get();

        $totalIncome = $payments->sum('amount_paid');
        $totalExpenses = $expenses->sum('amount');
        
        $totalUnits = Unit::count();
        $activeLeases = Lease::where('status', LeaseStatus::Active)->count();
        $occupancyRate = $totalUnits > 0 ? round(($activeLeases / $totalUnits) * 100) : 0;

        $data = [
            'payments' => $payments,
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $totalIncome - $totalExpenses,
            'monthName' => date('F', mktime(0, 0, 0, $month, 10)),
            'year' => $year,
            'activeTenants' => $activeLeases,
            'occupancyRate' => $occupancyRate
        ];

        return Pdf::loadView('pdf.owner_statement', $data);
    }
}
