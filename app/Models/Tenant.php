<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'occupation',
        'national_id',
        'address',
        'emergency_contact',
        'family_members',
    ];

    protected static function booted(): void
    {
        if (auth()->check()) {
            static::addGlobalScope('user', function (\Illuminate\Database\Eloquent\Builder $builder) {
                $builder->where('user_id', auth()->id());
            });
        }
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
   
}
