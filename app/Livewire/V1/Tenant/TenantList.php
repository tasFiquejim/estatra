<?php

namespace App\Livewire\V1\Tenant;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Traits\HasSortingAndSearch;

#[Layout('layouts.app')]

#[Title('Tenants')]

class TenantList extends Component
{
    use HasSortingAndSearch;

    public function deleteTenant(Tenant $tenant): void
    {
        try {
            $tenant->delete();
            $this->dispatch('tenant-deleted', [
                'message' => 'Tenant deleted successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('tenant-error', [
                'message' => 'Failed to delete tenant. Please try again.'
            ]);
        }
    }
    #[Computed]

    public function tenants()
    {
        $query = Tenant::query();

        $query = $this->applySearch($query, ['first_name', 'last_name', 'email', 'phone', 'national_id']);
        $query = $this->applySorting($query);

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.v1.tenant.tenant-list');
    }
}
