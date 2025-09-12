<?php

namespace App\Livewire\Lease;

use App\Models\Unit;
use App\Models\Lease;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
#[Title('Leases')]
class LeaseForm extends Component
{
    public ?Lease $lease = null;
    public string $unit_id = '';
    public string $tenant_id = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $security_deposit = '';
    public string $rent_amount = '';
    public string $service_charge = '';
    public string $status = 'active';

    public bool $isEdit = false;

    protected function rules()
    {
        $rules = [
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => $this->isEdit
                ? ['required', 'date']
                : ['required', 'date', 'after_or_equal:today'],
            'end_date' => 'nullable|date|after:start_date',
            'security_deposit' => 'nullable|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,expired,terminated',
        ];
    }
    public function title(): string
    {
        return $this->isEdit ? 'Edit Lease' : 'Add New Lease';
    }
    public function mount(Lease $lease): void
    {
        if ($lease && $lease->exists) {
            $this->lease = $lease;
            $this->isEdit = true;
            $this->fill([
                'unit_id' => $lease->unit_id,
                'tenant_id' => $lease->tenant_id,
                'start_date' => $lease->start_date->format('Y-m-d'),
                'end_date' => $lease->end_date?->format('Y-m-d') ?? '',
                'security_deposit' => $lease->security_deposit ?? '',
                'rent_amount' => $lease->rent_amount,
                'service_charge' => $lease->service_charge ?? '',
                'status' => $lease->status,
            ]);
        }
    }

    public function save()
    {
        $validated = $this->validate();

        if (!$this->isEdit) {
            $unit = Unit::find($validated['unit_id']);
            if ($unit->status !== 'available') {
                $this->addError('unit_id', 'This unit is not available for lease.');
                return;
            }
        }

        if ($this->isEdit) {
            $this->lease->update($validated);
            $this->updateUnitStatus();
            $message = 'Lease updated successfully!';
        } else {
            Lease::create($validated);
            Unit::find($validated['unit_id'])->update(['status' => 'occupied']);
            $message = 'Lease created successfully!';
        }

        session()->flash('success', $message);
        return $this->redirect(route('lease.index'));
    }

    private function updateUnitStatus(): void
    {
        $unit = $this->lease->unit;

        if ($this->status === 'active') {
            $unit->update(['status' => 'occupied']);
        } elseif (in_array($this->status, ['expired', 'terminated'])) {
            $unit->update(['status' => 'available']);
        }
    }
    public function cancel()
    {
        return $this->redirect(route('lease.index'));
    }
    #[Computed]
    public function getAvailableUnitsProperty()
    {
        if ($this->isEdit) {
            return Unit::with('property')
                ->where(function ($query) {
                    $query->where('status', 'available')
                        ->orWhere('id', $this->lease->unit_id);
                })
                ->get()
                ->groupBy('property.property_name');
        }

        return Unit::with('property')
            ->where('status', 'available')
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
        return view('livewire.lease.lease-form');
    }
}
