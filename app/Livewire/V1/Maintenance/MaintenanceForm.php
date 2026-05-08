<?php

namespace App\Livewire\V1\Maintenance;

use App\Models\Unit;
use Livewire\Component;
use App\Models\Property;
use Livewire\WithFileUploads;
use App\Models\MaintenanceLog;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.app')]

#[Title('Maintenance')]
class MaintenanceForm extends Component
{
    use WithFileUploads;

    public ?MaintenanceLog $maintenanceLog = null;

    public string $property_id = '';
    public string $unit_id = '';
    public string $maintenance_date = '';
    public string $issue_description = '';
    public string $product_cost = '';
    public string $service_cost = '';
    public string $total_cost = '';
    public string $status = 'pending';
    public $photo;

    public bool $isEdit = false;

    protected function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'unit_id' => 'nullable|exists:units,id',
            'maintenance_date' => 'required|date|before_or_equal:today',
            'issue_description' => 'required|string|max:1000',
            'product_cost' => 'nullable|numeric|min:0',
            'service_cost' => 'nullable|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function mount(MaintenanceLog $maintenanceLog)
    {
        $this->maintenance_date = now()->format('Y-m-d');

        $this->maintenanceLog = $maintenanceLog;

        if ($maintenanceLog->exists) {
            Gate::authorize('update', $maintenanceLog);
            $this->initializeForEdit($maintenanceLog);
        } else {
            Gate::authorize('create', MaintenanceLog::class);
        }
    }
    public function initializeForEdit(MaintenanceLog $maintenanceLog)
    {
        $this->isEdit = true;
        $this->fill([
            'property_id' => $maintenanceLog->property_id,
            'unit_id' => $maintenanceLog->unit_id ?? '',
            'maintenance_date' => $maintenanceLog->maintenance_date->format('Y-m-d'),
            'issue_description' => $maintenanceLog->issue_description,
            'product_cost' => $maintenanceLog->product_cost ?? '',
            'service_cost' => $maintenanceLog->service_cost ?? '',
            'total_cost' => $maintenanceLog->total_cost ?? '',
            'status' => $maintenanceLog->status,
        ]);
    }
    public function updatedPropertyId($value)
    {
        $this->unit_id = '';
    }

    public function updatedProductCost()
    {
        $this->calculateTotal();
    }

    public function updatedServiceCost()
    {
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $productCost = (float) $this->product_cost;
        $serviceCost = (float) $this->service_cost;
        $this->total_cost = $productCost + $serviceCost;
    }

    public function title(): string
    {
        return $this->isEdit ? 'Edit Maintenance Record' : 'Add Maintenance Record';
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->photo) {
            $validated['photo'] = $this->photo->store('maintenance', 'public');
        }

        if ($this->isEdit) {
            $this->maintenanceLog->update($validated);
            $message = 'Maintenance record updated successfully!';
        } else {
            MaintenanceLog::create($validated);
            $message = 'Maintenance record created successfully!';
        }

        session()->flash('success', $message);
        return $this->redirect(route('maintenance.index'));
    }

    public function cancel()
    {
        return $this->redirect(route('maintenance.index'));
    }

    #[Computed]
    public function properties()
    {
        return Property::forUser(auth()->id())
            ->orderBy('property_name')
            ->get()
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'name' => $property->property_name,
                ];
            });
    }

    #[Computed]
    public function units()
    {
        if (!$this->property_id) {
            return collect();
        }

        return Unit::where('property_id', $this->property_id)
            ->orderBy('unit_name')
            ->get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->unit_name,
                ];
            });
    }

    #[Computed]
    public function statuses()
    {
        return MaintenanceLog::getStatuses();
    }

    public function render()
    {
        return view('livewire.v1.maintenance.maintenance-form');
    }
}
