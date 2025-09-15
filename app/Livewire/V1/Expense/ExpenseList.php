<?php

namespace App\Livewire\V1\Expense;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\PropertyExpense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Traits\HasSortingAndSearch;

#[Layout('layouts.app')]

#[Title('Expenses')]
class ExpenseList extends Component
{
    use HasSortingAndSearch;

    #[Computed]
    public function expenses()
    {
        $query = PropertyExpense::with('property');
        $query = $this->applyAdvancedSearch($query, [
            ['field' => 'category'],
            ['field' => 'description'],
            ['field' => 'invoice_number'],
            ['relation' => 'property', 'fields' => ['property_name', 'address']]
        ]);
        $query = $this->applySorting($query);
        return $query->paginate(10);
    }
    public function render()
    {
        return view('livewire.v1.expense.expense-list');
    }
}
