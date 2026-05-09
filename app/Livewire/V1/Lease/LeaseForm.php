<?php

namespace App\Livewire\V1\Lease;

use App\Enums\LeaseStatus;
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
        return $this->form->isEdit ? 'Edit Lease' : 'Add New Lease';
    }

    public function mount(Lease $lease): void
    {
        if ($lease && $lease->exists) {
            Gate::authorize('update', $lease);
            $this->form->setLease($lease);
        } else {
            Gate::authorize('create', Lease::class);
        }
    }

    public function save(LeaseService $leaseService)
    {
        $validated = $this->form->validate();

        try {
            if ($this->form->isEdit) {
                $leaseService->updateLease($this->form->lease, $validated);
                $message = 'Lease updated successfully!';
            } else {
                $leaseService->createLease($validated);
                $message = 'Lease created successfully!';
            }

            session()->flash('success', $message);
            return $this->redirect(route('lease.index'));
        } catch (\Exception $e) {
            $this->addError('form.unit_id', $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('lease.index'));
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
                    $query->where('status', \App\Enums\UnitStatus::Available)
                        ->orWhere('id', $this->form->lease->unit_id);
                })
                ->get()
                ->groupBy('property.property_name');
        }

        return Unit::with('property')
            ->whereHas('property', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', \App\Enums\UnitStatus::Available)
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
                    'name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'email' => $tenant->email
                ];
            });
    }

    public function render()
    {
        return view('livewire.v1.lease.lease-form');
    }
}
