<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
   
}
