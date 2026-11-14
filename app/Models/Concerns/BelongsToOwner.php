<?php

namespace App\Models\Concerns;

use App\Models\Scopes\OwnedByUserScope;

/**
 * Add to any model that belongs to one owner (user), together with
 * "implements HasOwner".
 *
 * The model says how to reach the owner through ownerRelation():
 *  - null            → the model has its own user_id column (Property, Tenant)
 *  - 'property'      → through a relation (Unit, PropertyExpense, ...)
 *  - 'unit.property' → through nested relations (Lease, Payment)
 */
trait BelongsToOwner
{
    public static function bootBelongsToOwner(): void
    {
        static::addGlobalScope('owner', new OwnedByUserScope);
    }
}
