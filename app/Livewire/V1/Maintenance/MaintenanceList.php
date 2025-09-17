<?php

namespace App\Livewire\V1\Maintenance;

use Livewire\Component;
use App\Models\MaintenanceLog;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Traits\HasSortingAndSearch;

#[Layout('layouts.app')]

#[Title('Maintenance')]
class MaintenanceList extends Component
{
    use HasSortingAndSearch;

    #[Computed]
    public function maintenanceLogs()
    {
        $query = MaintenanceLog::with(['property', 'unit']);

        $query->whereHas('property', function ($q) {
            $q->forUser(auth()->id());
        });

        $query = $this->applyAdvancedSearch($query, [
            ['field' => 'issue_description'],
            ['field' => 'status'],
            ['relation' => 'property', 'fields' => ['property_name']],
            ['relation' => 'unit',     'fields' => ['unit_name']],
        ]);

        $query = $this->applySorting($query);
        return $query->paginate(10);
    }
    public function render()
    {
        return view('livewire.v1.maintenance.maintenance-list');
    }
}
