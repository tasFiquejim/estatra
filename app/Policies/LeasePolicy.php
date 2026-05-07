<?php

namespace App\Policies;

use App\Models\Lease;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeasePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Lease $lease)
    {
        return $user->id === $lease->unit->property->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Lease $lease)
    {
        return $user->id === $lease->unit->property->user_id;
    }

    public function delete(User $user, Lease $lease)
    {
        return $user->id === $lease->unit->property->user_id;
    }
}
