<?php

namespace App\Livewire\V1\Unit;

use App\Models\Property;
use App\Models\Unit;
use App\Traits\HasSortingAndSearch;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UnitList extends Component
{
    use HasSortingAndSearch;

    public Property $property;

    public function mount(Property $property)
    {
        $this->property = $property;
    }

    public function deleteUnit(Unit $unit): void
    {
        $unit->delete();
        $this->dispatch('unit-deleted', [
            'message' => 'Unit deleted successfully!',
        ]);
    }

    #[Computed]
    public function units()
    {
        $query = Unit::query()->with('activeLease.tenant')->whereBelongsTo($this->property);
        $query = $this->applySearch($query, ['unit_name', 'floor_number', 'size']);
        $query = $this->applySorting($query);

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.v1.unit.unit-list');
    }
}
