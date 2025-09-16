<?php

namespace App\Livewire\V1\Tenant;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]

#[Title('Tenants')]
class TenantForm extends Component
{
    public ?Tenant $tenant = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $occupation = '';
    public string $national_id = '';
    public string $address = '';
    public string $emergency_contact = '';
    public string $family_members = '';

    public bool $isEdit = false;

    public function mount(Tenant $tenant): void
    {
        if ($tenant && $tenant->exists) {
            $this->tenant = $tenant;
            $this->isEdit = true;
            $this->fill([
                'first_name' => $tenant->first_name,
                'last_name' => $tenant->last_name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'occupation' => $tenant->occupation ?? '',
                'national_id' => $tenant->national_id ?? '',
                'address' => $tenant->address ?? '',
                'emergency_contact' => $tenant->emergency_contact ?? '',
                'family_members' => $tenant->family_members ?? '',
            ]);
        }
    }

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'national_id' => 'required|digits_between:10,17',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'family_members' => 'nullable|int|',
        ];

        if ($this->isEdit) {
            $rules['email'] = 'required|email|max:255|unique:tenants,email,' . $this->tenant->id;
        } else {
            $rules['email'] = 'required|email|max:255|unique:tenants,email';
        }

        return $rules;
    }

    public function title(): string
    {
        return $this->isEdit ? 'Edit Tenant' : 'Add New Tenant';
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            $this->tenant->update($validated);
            $message = 'Tenant updated successfully!';
        } else {
            Tenant::create($validated);
            $message = 'Tenant created successfully!';
        }

        session()->flash('success', $message);
        return $this->redirect(route('tenant.index'));
    }

    public function cancel()
    {
        return $this->redirect(route('tenant.index'));
    }
    public function render()
    {
        return view('livewire.v1.tenant.tenant-form');
    }
}
