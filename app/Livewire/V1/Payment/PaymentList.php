<?php

namespace App\Livewire\V1\Payment;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Traits\HasSortingAndSearch;

#[Layout('layouts.app')]

#[Title('Payments')]
class PaymentList extends Component

{
    use HasSortingAndSearch;

    #[Computed]
    public function payments()
    {
        $query = Payment::with(['lease.unit.property', 'lease.tenant']);

        $query = $this->applyAdvancedSearch($query, [
            ['field' => 'status'],
            ['field' => 'payment_method'],
            ['field' => 'receipt_number'],
            ['relation' => 'lease.tenant', 'fields' => ['first_name', 'last_name']],
            ['relation' => 'lease.unit.property', 'fields' => ['property_name']],
            ['relation' => 'lease.unit', 'fields' => ['unit_name']],
        ]);

        $query = $this->applySorting($query);
        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.v1.payment.payment-list');
    }
}
