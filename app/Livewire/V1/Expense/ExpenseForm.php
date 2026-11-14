<?php

namespace App\Livewire\V1\Expense;

use App\Enums\RepairStatus;
use App\Models\Property;
use App\Models\PropertyExpense;
use App\Models\Unit;
use App\Rules\OwnedRecord;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Expenses')]
class ExpenseForm extends Component
{
    use WithFileUploads;

    public ?PropertyExpense $propertyExpense = null;

    public string $property_id = '';

    public string $expense_date = '';

    public string $category = '';

    public $amount = '';

    public string $invoice_number = '';

    public string $description = '';

    // Only used for repairs (category "maintenance")
    public string $unit_id = '';

    public string $repair_status = 'open';

    public $photo;

    public bool $isEdit = false;

    protected function rules(): array
    {
        return [
            'property_id' => ['required', new OwnedRecord(Property::class)],
            'expense_date' => 'required|date|before_or_equal:today',
            'category' => ['required', Rule::in(array_keys(PropertyExpense::getCategories()))],
            // 0 is allowed: a repair can be logged before anyone is paid to fix it.
            'amount' => 'required|numeric|min:0',
            'description' => $this->isRepair() ? 'required|string|max:1000' : 'nullable|string|max:1000',
            'unit_id' => ['nullable', function (string $attribute, mixed $value, Closure $fail) {
                if (! Unit::whereKey($value)->where('property_id', $this->property_id)->exists()) {
                    $fail('Choose a unit in the selected property.');
                }
            }],
            'repair_status' => ['nullable', Rule::enum(RepairStatus::class)],
            'photo' => 'nullable|image|max:2048',
        ];
    }

    protected function messages(): array
    {
        return [
            'description.required' => 'Describe the problem, e.g. "Leaking pipe in bathroom".',
        ];
    }

    public function mount(PropertyExpense $propertyExpense)
    {
        $this->expense_date = now()->format('Y-m-d');
        $this->propertyExpense = $propertyExpense;
        if ($propertyExpense->exists) {
            Gate::authorize('update', $propertyExpense);
            $this->initializeForEdit($propertyExpense);
        } else {
            Gate::authorize('create', PropertyExpense::class);
            // "Log a repair" link from the list: ?category=maintenance
            if (request()->query('category') === PropertyExpense::REPAIR) {
                $this->category = PropertyExpense::REPAIR;
            }
        }
    }

    public function updatedPropertyId(): void
    {
        $this->unit_id = '';
    }

    public function save()
    {
        if (! $this->isEdit && empty($this->invoice_number)) {
            $this->invoice_number = $this->generateInvoiceNo();
        }

        $validated = $this->validate();
        unset($validated['photo']);

        if ($this->isRepair()) {
            $validated['unit_id'] = $validated['unit_id'] ?: null;
        } else {
            // Not a repair: the repair-only fields don't apply.
            $validated['unit_id'] = null;
            $validated['repair_status'] = null;
        }

        if ($this->photo) {
            if ($this->isEdit && $this->propertyExpense->photo) {
                Storage::disk('public')->delete($this->propertyExpense->photo);
            }
            $validated['photo'] = $this->photo->store('expenses', 'public');
        }

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
            'property_id' => (string) $propertyExpense->property_id,
            'expense_date' => $propertyExpense->expense_date->format('Y-m-d'),
            'category' => $propertyExpense->category,
            'amount' => $propertyExpense->amount,
            'description' => $propertyExpense->description ?? '',
            'unit_id' => (string) ($propertyExpense->unit_id ?? ''),
            // Older repair costs have no status: they were paid, so treat them as fixed.
            'repair_status' => $propertyExpense->repair_status->value ?? RepairStatus::Done->value,
        ]);
    }

    public function isRepair(): bool
    {
        return $this->category === PropertyExpense::REPAIR;
    }

    public function title(): string
    {
        return $this->isEdit ? 'Edit Expense' : 'Add New Expense';
    }

    private function generateInvoiceNo()
    {
        // Invoice numbers are unique across all users, so look past the owner scope.
        $lastNumber = PropertyExpense::withoutGlobalScope('owner')
            ->whereNotNull('invoice_number')
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastNumber ? ((int) $lastNumber->invoice_number) + 1 : 1;

        return str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    #[Computed]
    public function properties()
    {
        return Property::orderBy('property_name')
            ->get()
            ->map(fn (Property $property) => [
                'id' => $property->id,
                'name' => $property->property_name,
                'address' => $property->address,
            ]);
    }

    #[Computed]
    public function units()
    {
        if (! $this->property_id) {
            return collect();
        }

        return Unit::where('property_id', $this->property_id)->orderBy('unit_name')->get(['id', 'unit_name']);
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
