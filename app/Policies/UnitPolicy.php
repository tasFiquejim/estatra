<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Unit $unit)
    {
        return $user->id === $unit->property->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Unit $unit)
    {
        return $user->id === $unit->property->user_id;
    }

    public function delete(User $user, Unit $unit)
    {
        return $user->id === $unit->property->user_id;
    }
}
