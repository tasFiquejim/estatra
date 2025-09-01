<?php

namespace App\Livewire\Property;

use Livewire\Component;
use App\Models\Property;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
#[Layout('layouts.app')]
#[Title('Properties')]
class PropertyList extends Component
{
    use WithPagination;
    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteProperty(Property $property): void
    {
        $this->authorize('delete', $property);

        $property->delete();

        $this->dispatch('property-deleted', [
            'message' => 'Property deleted successfully!'
        ]);
    }
    #[Computed]
    public function properties()
    {
        return Property::query()
            ->forUser(auth()->id())
            ->when($this->search, function ($query) {
                $query->where('property_name', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.property.property-list');
    }
}
