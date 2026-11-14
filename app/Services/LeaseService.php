<?php

namespace App\Services;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class LeaseService
{
    public function createLease(array $data): Lease
    {
        return DB::transaction(function () use ($data) {
            $unit = Unit::lockForUpdate()->find($data['unit_id']);

            if ($unit === null || $unit->status !== UnitStatus::Available) {
                throw new Exception('This unit is not available for lease.');
            }

            $lease = Lease::create($data);
            $unit->update(['status' => UnitStatus::Occupied]);

            return $lease;
        });
    }

    public function updateLease(Lease $lease, array $data): Lease
    {
        return DB::transaction(function () use ($lease, $data) {
            $previousUnitId = $lease->unit_id;

            $lease->fill($data);

            // An ended lease needs an end date, otherwise we can't tell
            // which months rent was still due for.
            if ($lease->status !== LeaseStatus::Active && $lease->end_date === null) {
                $lease->fill(['end_date' => now()->toDateString()]);
            }

            $lease->save();

            // Tenant moved to a different unit: free the old one.
            if ($lease->wasChanged('unit_id')) {
                Unit::whereKey($previousUnitId)->update(['status' => UnitStatus::Available]);
            }

            $lease->load('unit');
            $lease->unit->update([
                'status' => $lease->status === LeaseStatus::Active ? UnitStatus::Occupied : UnitStatus::Available,
            ]);

            return $lease;
        });
    }

    /**
     * Move a tenant into a free unit. Pass an existing Tenant, or an array
     * with a new tenant's details to create them in the same step.
     */
    public function moveIn(Unit $unit, Tenant|array $tenant, array $terms): Lease
    {
        return DB::transaction(function () use ($unit, $tenant, $terms) {
            if (is_array($tenant)) {
                $tenant = Tenant::create([...$tenant, 'user_id' => $unit->property->user_id]);
            }

            return $this->createLease([
                ...$terms,
                'unit_id' => $unit->id,
                'tenant_id' => $tenant->id,
                'status' => LeaseStatus::Active,
            ]);
        });
    }

    /**
     * End a lease: record the move-out date and the deposit given back, free the unit.
     * Leaving on or after the planned end date counts as "expired", earlier as "terminated".
     */
    public function moveOut(Lease $lease, CarbonInterface $date, ?float $depositReturned): Lease
    {
        if ($lease->status !== LeaseStatus::Active) {
            throw new Exception('This tenant has already moved out.');
        }

        return DB::transaction(function () use ($lease, $date, $depositReturned) {
            $plannedEnd = $lease->end_date;

            $lease->fill([
                'end_date' => $date->toDateString(),
                'status' => $plannedEnd !== null && $date->greaterThanOrEqualTo($plannedEnd)
                    ? LeaseStatus::Expired
                    : LeaseStatus::Terminated,
                'deposit_returned' => $depositReturned,
            ])->save();

            $lease->unit->update(['status' => UnitStatus::Available]);

            return $lease;
        });
    }

    public function deleteLease(Lease $lease): void
    {
        DB::transaction(function () use ($lease) {
            $lease->unit->update(['status' => UnitStatus::Available]);
            $lease->delete();
        });
    }

    /**
     * Active leases as id => label pairs, for a <select>.
     * The owner scope already limits these to the logged-in user.
     */
    public function activeLeaseOptions(): array
    {
        return Lease::with(['unit.property', 'tenant'])
            ->where('status', LeaseStatus::Active)
            ->get()
            ->map(fn (Lease $lease) => [
                'id' => $lease->id,
                'display' => $lease->unit->property->property_name
                    .' - Unit '.$lease->unit->unit_name
                    .' ('.$lease->tenant?->first_name.' '.$lease->tenant?->last_name.')',
            ])
            ->toArray();
    }

    public function calculateTotalRent(Lease $lease): float
    {
        return $lease->monthlyCharge();
    }
}
