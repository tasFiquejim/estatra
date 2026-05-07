<?php

namespace App\Services;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Models\Lease;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Exception;

class LeaseService
{
    public function createLease(array $data): Lease
    {
        return DB::transaction(function () use ($data) {
            $unit = Unit::lockForUpdate()->find($data['unit_id']);

            if ($unit->status !== UnitStatus::Available) {
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
            $lease->update($data);

            $unit = $lease->unit;
            if ($lease->status === LeaseStatus::Active) {
                $unit->update(['status' => UnitStatus::Occupied]);
            } elseif (in_array($lease->status, [LeaseStatus::Expired, LeaseStatus::Terminated])) {
                $unit->update(['status' => UnitStatus::Available]);
            }

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
}
