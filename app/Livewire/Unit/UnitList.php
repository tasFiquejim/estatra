<?php

namespace App\Livewire\Unit;

use App\Models\Unit;
use Livewire\Component;
use App\Models\Property;
use App\Traits\HasSortingAndSearch;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

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
            'message' => 'Unit deleted successfully!'
        ]);
    }

    #[Computed]
    public function units()
    {
        $query = Unit::query()->whereBelongsTo($this->property); 
        $query = $this->applySearch($query, ['unit_name', 'floor_number', 'size']);
        $query = $this->applySorting($query);
        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.unit.unit-list');
    }
}
