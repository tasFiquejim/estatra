<?php

namespace App\Policies;

use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->id === $maintenanceLog->property->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->id === $maintenanceLog->property->user_id;
    }

    public function delete(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->id === $maintenanceLog->property->user_id;
    }
}
