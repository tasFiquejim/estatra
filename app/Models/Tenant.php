<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
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

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
   
}
