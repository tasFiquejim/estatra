<?php

namespace App\Policies;

use App\Models\PropertyExpense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PropertyExpense $propertyExpense): bool
    {
        return $user->id === $propertyExpense->property->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PropertyExpense $propertyExpense): bool
    {
        return $user->id === $propertyExpense->property->user_id;
    }

    public function delete(User $user, PropertyExpense $propertyExpense): bool
    {
        return $user->id === $propertyExpense->property->user_id;
    }
}
