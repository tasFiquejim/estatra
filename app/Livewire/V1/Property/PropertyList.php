<?php

namespace App\Livewire\V1\Property;

use Livewire\Component;
use App\Models\Property;
use App\Traits\HasSortingAndSearch;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]

#[Title('Properties')]

class PropertyList extends Component
{
    use HasSortingAndSearch;

    public function deleteProperty(Property $property): void
    {
        try {
            $property->delete();
            $this->dispatch('property-deleted', [
                'message' => 'Property deleted successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('property-error', [
                'message' => 'Failed to delete property. Please try again.'
            ]);
        }
    }
    #[Computed]
    public function properties()
    {
        $query = Property::query();

        $query = $this->applySearch($query, ['property_name', 'address']);
        $query = $this->applySorting($query);

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.v1.property.property-list');
    }
}
