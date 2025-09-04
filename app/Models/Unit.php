<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'property_id',
        'unit_name',
        'floor_number',
        'size',
        'rent_amount',
        'service_charge',
        'notes',
        'status',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
    
    public function scopeisAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
