<?php

namespace App\Livewire\V1\Dashboard;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyExpense;
use App\Models\Unit;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    #[Computed]
    public function totalProperties(): int
    {
        return Property::forUser(auth()->id())->count();
    }

    #[Computed]
    public function totalUnits(): int
    {
        return Unit::whereHas('property', fn($q) => $q->forUser(auth()->id()))->count();
    }

    #[Computed]
    public function occupiedUnits(): int
    {
        return Unit::whereHas('property', fn($q) => $q->forUser(auth()->id()))
            ->where('status', UnitStatus::Occupied)
            ->count();
    }

    #[Computed]
    public function occupancyRate(): float
    {
        $total = $this->totalUnits;
        if ($total === 0) return 0;

        return round(($this->occupiedUnits / $total) * 100, 1);
    }

    #[Computed]
    public function activeTenantsCount(): int
    {
        return Lease::whereHas('unit.property', fn($q) => $q->forUser(auth()->id()))
            ->where('status', LeaseStatus::Active)
            ->count();
    }

    #[Computed]
    public function monthlyIncome(): float
    {
        return (float) Payment::whereHas('lease.unit.property', fn($q) => $q->forUser(auth()->id()))
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_paid');
    }

    #[Computed]
    public function monthlyExpenses(): float
    {
        return (float) PropertyExpense::whereHas('property', fn($q) => $q->forUser(auth()->id()))
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }

    #[Computed]
    public function netProfit(): float
    {
        return $this->monthlyIncome - $this->monthlyExpenses;
    }

    #[Computed]
    public function expiringSoonLeases()
    {
        return Lease::with(['unit.property', 'tenant'])
            ->whereHas('unit.property', fn($q) => $q->forUser(auth()->id()))
            ->where('status', LeaseStatus::Active)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->orderBy('end_date')
            ->get();
    }

    #[Computed]
    public function recentPayments()
    {
        return Payment::with(['lease.tenant', 'lease.unit.property'])
            ->whereHas('lease.unit.property', fn($q) => $q->forUser(auth()->id()))
            ->latest('payment_date')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.v1.dashboard.dashboard');
    }
}
