<?php

namespace App\Livewire\V1\Expense;

use App\Models\PropertyExpense;
use App\Traits\HasSortingAndSearch;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]

#[Title('Expenses')]
class ExpenseList extends Component
{
    use HasSortingAndSearch;

    /** Show only repairs that still need fixing (replaces the old Maintenance screen). */
    #[Url(except: false)]
    public bool $openRepairs = false;

    public function updatingOpenRepairs(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function openRepairsCount(): int
    {
        return PropertyExpense::openRepairs()->count();
    }

    #[Computed]
    public function expenses()
    {
        $query = PropertyExpense::with(['property', 'unit'])
            ->when($this->openRepairs, fn ($q) => $q->openRepairs());
        $query = $this->applyAdvancedSearch($query, [
            ['field' => 'category'],
            ['field' => 'description'],
            ['field' => 'invoice_number'],
            ['relation' => 'property', 'fields' => ['property_name', 'address']],
        ]);
        $query = $this->applySorting($query);

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.v1.expense.expense-list');
    }
}
