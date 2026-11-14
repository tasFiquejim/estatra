<?php

namespace App\Livewire\V1\Lease;

use App\Enums\UnitStatus;
use App\Livewire\Forms\LeaseFormObject;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\LeaseService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Leases')]
class LeaseForm extends Component
{
    public LeaseFormObject $form;

    public function title(): string
    {
        return 'Edit rent agreement';
    }

    /**
     * Edit-only: tenants move in through "Move in" on a unit, and leave through "Move out".
     */
    public function mount(Lease $lease): void
    {
        abort_unless($lease->exists, 404);
        Gate::authorize('update', $lease);
        $this->form->setLease($lease);
    }

    public function save(LeaseService $leaseService)
    {
        $validated = $this->form->validate();

        try {
            $leaseService->updateLease($this->form->lease, $validated);
            $message = 'Rent agreement updated.';

            session()->flash('success', $message);

            return $this->redirect(route('property.show', $this->form->lease->unit->property_id));
        } catch (\Exception $e) {
            $this->addError('form.unit_id', $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('property.show', $this->form->lease->unit->property_id));
    }

    #[Computed]
    public function getAvailableUnitsProperty()
    {
        $userId = auth()->id();

        if ($this->form->isEdit) {
            return Unit::with('property')
                ->whereHas('property', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->where(function ($query) {
                    $query->where('status', UnitStatus::Available)
                        ->orWhere('id', $this->form->lease->unit_id);
                })
                ->get()
                ->groupBy('property.property_name');
        }

        return Unit::with('property')
            ->whereHas('property', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', UnitStatus::Available)
            ->get()
            ->groupBy('property.property_name');
    }

    #[Computed]
    public function getTenantsProperty()
    {
        return Tenant::orderBy('first_name')
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->first_name.' '.$tenant->last_name,
                    'email' => $tenant->email,
                ];
            });
    }

    public function render()
    {
        return view('livewire.v1.lease.lease-form');
    }
}
