<?php

namespace App\Livewire\V1\Expense;

use Livewire\Component;
use App\Models\Property;
use Livewire\Attributes\Title;
use App\Models\PropertyExpense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]

#[Title('Expenses')]
class ExpenseForm extends Component
{
    public ?PropertyExpense $propertyExpense = null;
    public string $property_id = '';
    public string $expense_date = '';
    public string $category = '';
    public string $amount = '';
    public string $invoice_number = '';
    public string $description = '';
    public bool $isEdit = false;

    protected function rules(): array
    {
        $rules = [
            'property_id' => 'required|exists:properties,id',
            'expense_date' => 'required|date|before_or_equal:today',
            'category' => 'required|in:maintenance,utilities,cleaning,security,insurance,taxes,water,electricity,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
        return $rules;
    }

    public function mount(PropertyExpense $propertyExpense)
    {
        $this->expense_date = now()->format('Y-m-d');
        $this->propertyExpense = $propertyExpense;
        if ($propertyExpense && $propertyExpense->exists) {
            $this->initializeForEdit($propertyExpense);
        }
    }

    public function save()
    {
        if (!$this->isEdit && empty($this->invoice_number)) {
            $this->invoice_number = $this->generateInvoiceNo();
        }

        $validated = $this->validate();

        if ($this->isEdit) {
            $this->propertyExpense->update($validated);
            $message = 'Expense record updated successfully!';
        } else {
            $validated['invoice_number'] = $this->invoice_number;
            PropertyExpense::create($validated);
            $message = 'Expense recorded successfully!';
        }
        session()->flash('success', $message);

        return $this->redirect(route('expense.index'));
    }

    public function cancel()
    {
        return $this->redirect(route('expense.index'));
    }

    public function initializeForEdit(PropertyExpense $propertyExpense): void
    {
        $this->isEdit = true;

        $this->fill([
            'property_id' => $propertyExpense->property_id,
            'expense_date' => $propertyExpense->expense_date->format('Y-m-d'),
            'category' => $propertyExpense->category,
            'amount' => $propertyExpense->amount,
            'description' => $propertyExpense->description ?? '',
        ]);
    }

    public function title(): string
    {
        return $this->isEdit ? 'Edit Expense' : 'Add New Expense';
    }

    private function generateInvoiceNo()
    {
        $lastNumber = PropertyExpense::whereNotNull('invoice_number')
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastNumber ? ((int)$lastNumber->invoice_number) + 1 : 1;

        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    #[Computed]
    public function properties()
    {
        return Property::forUser(auth()->id())
            ->orderBy('property_name')
            ->get()
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->property_name,
                    'address' => $property->address
                ];
            });
    }

    #[Computed]
    public function expenseCategories()
    {
        return PropertyExpense::getCategories();
    }

    public function render()
    {
        return view('livewire.v1.expense.expense-form');
    }
}
