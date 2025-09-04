<?php

namespace App\Livewire\Unit;

use App\Models\Unit;
use Livewire\Component;
use App\Models\Property;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class UnitList extends Component
{
    use WithPagination;
    public Property $property;
    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(Property $property)
    {
        $this->property = $property;
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function sortByField(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
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
        return Unit::query()
            ->whereBelongsTo($this->property) // same scope as $this->property->units()
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where(function ($q) use ($s) {
                    $q->where('unit_name', 'like', $s)
                        ->orWhere('floor_number', 'like', $s)
                        ->orWhere('size', 'like', $s); // if size is numeric, consider removing LIKE
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.unit.unit-list');
    }
}
