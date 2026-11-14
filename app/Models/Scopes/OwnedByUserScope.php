<?php

namespace App\Models\Scopes;

use App\Models\Contracts\HasOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Limits every query to records owned by the logged-in user.
 *
 * The user is checked each time a query runs (not once when the model boots),
 * so the scope is correct no matter who was logged in when the model was
 * first used. With no logged-in user (queue workers, console) it does nothing.
 */
class OwnedByUserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $userId = Auth::id();

        if ($userId === null || ! $model instanceof HasOwner) {
            return;
        }

        $relation = $model->ownerRelation();

        if ($relation === null) {
            $builder->where($model->qualifyColumn('user_id'), $userId);

            return;
        }

        $builder->whereHas($relation, fn (Builder $query) => $query->where('user_id', $userId));
    }
}
