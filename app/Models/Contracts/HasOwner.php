<?php

namespace App\Models\Contracts;

/**
 * A model that belongs to one owner (user). See the BelongsToOwner trait.
 */
interface HasOwner
{
    /**
     * Relation path to the model that holds user_id,
     * or null when this model has a user_id column itself.
     */
    public function ownerRelation(): ?string;
}
