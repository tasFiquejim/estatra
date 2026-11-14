<?php

namespace App\Livewire\V1\Unit;

use App\Enums\UnitStatus;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Units')]
class UnitForm extends Component
{
    public ?Unit $unit = null;

    public Property $property;

    public string $unit_name = '';

    public string $floor_number = '';

    public string $size = '';

    public string $notes = '';

    public string $status = 'available';

    public bool $isEdit = false;

    protected function rules()
    {
        return [
            'unit_name' => 'required|string|max:255',
            'floor_number' => 'nullable|string|max:10',
            'size' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
            'status' => ['required', Rule::enum(UnitStatus::class)],
        ];
    }

    public function mount(Property $property, ?Unit $unit): void
    {
        $this->property = $property;
        Gate::authorize('update', $property);

        if ($unit->exists) {
            Gate::authorize('update', $unit);
            $this->unit = $unit;
            $this->isEdit = true;
            $this->fill([
                'unit_name' => $unit->unit_name,
                // Optional fields can be NULL in the database; the form fields are strings.
                'floor_number' => (string) ($unit->floor_number ?? ''),
                'size' => (string) ($unit->size ?? ''),
                'notes' => (string) ($unit->notes ?? ''),
                'status' => $unit->status->value ?? $this->status,
            ]);
        }
    }

    public function title(): string
    {
        return $this->isEdit ? 'Edit Unit' : 'Add Unit to '.$this->property->property_name;
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['property_id'] = $this->property->id;

        // Empty optional fields are saved as NULL, not "" (MySQL rejects "" for the numeric size column).
        $validated = array_map(fn ($value) => $value === '' ? null : $value, $validated);

        if ($this->isEdit) {
            $this->unit->update($validated);
            $message = 'Unit updated successfully!';
        } else {
            Unit::create($validated);
            $message = 'Unit added successfully!';
        }

        session()->flash('success', $message);

        return $this->redirect(route('property.show', $this->property));
    }

    public function cancel()
    {
        return $this->redirect(route('property.show', $this->property));
    }

    public function render()
    {
        return view('livewire.v1.unit.unit-form');
    }
}
