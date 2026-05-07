<?php

namespace App\Livewire\Forms;

use App\Enums\LeaseStatus;
use App\Models\Lease;
use Illuminate\Validation\Rule;
use Livewire\Form;

class LeaseFormObject extends Form
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

    public function rules()
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => $this->isEdit
                ? ['required', 'date']
                : ['required', 'date', 'after_or_equal:today'],
            'end_date' => 'nullable|date|after:start_date',
            'security_deposit' => 'nullable|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::enum(LeaseStatus::class)],
        ];
    }

    public function setLease(Lease $lease)
    {
        $this->lease = $lease;
        $this->isEdit = true;

        $this->unit_id = $lease->unit_id;
        $this->tenant_id = $lease->tenant_id;
        $this->start_date = $lease->start_date->format('Y-m-d');
        $this->end_date = $lease->end_date?->format('Y-m-d') ?? '';
        $this->security_deposit = $lease->security_deposit ?? '';
        $this->rent_amount = $lease->rent_amount;
        $this->service_charge = $lease->service_charge ?? '';
        $this->status = $lease->status?->value ?? 'active';
    }
}
