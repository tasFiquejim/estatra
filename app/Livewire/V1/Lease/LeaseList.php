<?php

namespace App\Livewire\V1\Lease;

use App\Enums\UnitStatus;
use App\Models\Lease;
use App\Traits\HasSortingAndSearch;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Leases')]
class LeaseList extends Component
{
    use HasSortingAndSearch;

    public function deleteLease(Lease $lease): void
    {
        try {
            $lease->unit->update(['status' => UnitStatus::Available]);

            $lease->delete();
            $this->dispatch('lease-deleted', [
                'message' => 'Lease deleted successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('lease-error', [
                'message' => 'Failed to delete lease. Please try again.'
            ]);
        }
    }

    #[Computed]
    public function leases()
    {
        $query = Lease::with(['unit.property', 'tenant']);

        $query = $this->applyAdvancedSearch($query, [
            ['field' => 'status'],
            ['relation' => 'tenant', 'fields' => ['first_name', 'last_name']],
            ['relation' => 'unit.property', 'fields' => ['property_name']]
        ]);

        $query = $this->applySorting($query);
        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.v1.lease.lease-list');
    }
}
