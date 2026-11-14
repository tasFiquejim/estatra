<?php

namespace App\Livewire\V1\Lease;

use App\Enums\UnitStatus;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Rules\OwnedRecord;
use App\Services\LeaseService;
use Exception;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * "Move in tenant": one form that adds the tenant (or picks an existing one)
 * and sets the rent, instead of separate Tenant and Lease screens.
 */
#[Layout('layouts.app')]
#[Title('Move in')]
class MoveIn extends Component
{
    public Property $property;

    public Unit $unit;

    /** 'new' = type the tenant's details, 'existing' = pick from your tenants */
    public string $tenantMode = 'new';

    public $tenant_id = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    public string $email = '';

    public string $national_id = '';

    public $rent_amount = '';

    public $service_charge = '';

    public $security_deposit = '';

    public string $start_date = '';

    public string $end_date = '';

    public function mount(Property $property, Unit $unit)
    {
        abort_unless($unit->property->is($property), 404);
        Gate::authorize('update', $unit);

        if ($unit->status !== UnitStatus::Available) {
            session()->flash('success', "Unit {$unit->unit_name} is not free, so no one can move in.");

            return $this->redirect(route('property.show', $property));
        }

        $this->start_date = now()->toDateString();

        // Most owners re-let at the same price: start from the last rent for this unit.
        $previous = $unit->leases()->latest('start_date')->first();
        if ($previous !== null) {
            $this->rent_amount = $previous->rent_amount;
            $this->service_charge = $previous->service_charge ?? '';
            $this->security_deposit = $previous->security_deposit ?? '';
        }
    }

    protected function rules(): array
    {
        $tenantRules = $this->tenantMode === 'existing'
            ? ['tenant_id' => ['required', new OwnedRecord(Tenant::class)]]
            : [
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'national_id' => 'nullable|digits_between:10,17',
            ];

        return [
            ...$tenantRules,
            'rent_amount' => 'required|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }

    protected function messages(): array
    {
        return [
            'tenant_id.required' => 'Choose a tenant, or switch to "New tenant".',
        ];
    }

    public function save(LeaseService $leases)
    {
        $data = $this->validate();

        $tenant = $this->tenantMode === 'existing'
            ? Tenant::findOrFail($data['tenant_id'])
            : collect($data)->only(['first_name', 'last_name', 'phone', 'email', 'national_id'])
                ->map(fn ($value) => $value === '' ? null : $value)
                ->all();

        $terms = collect($data)->only(['rent_amount', 'service_charge', 'security_deposit', 'start_date', 'end_date'])
            ->map(fn ($value) => $value === '' ? null : $value)
            ->all();

        try {
            $lease = $leases->moveIn($this->unit, $tenant, $terms);
        } catch (Exception $e) {
            $this->addError('rent_amount', $e->getMessage());

            return;
        }

        session()->flash('success', $lease->tenant->first_name.' moved into Unit '.$this->unit->unit_name.'.');

        return $this->redirect(route('property.show', $this->property));
    }

    #[Computed]
    public function tenants()
    {
        return Tenant::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'phone']);
    }

    public function render()
    {
        return view('livewire.v1.lease.move-in');
    }
}
