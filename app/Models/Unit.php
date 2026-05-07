<?php

namespace App\Models;

use App\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'property_id',
        'unit_name',
        'floor_number',
        'size',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => UnitStatus::class,
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
    
    public function scopeisAvailable($query)
    {
        return $query->where('status', \App\Enums\UnitStatus::Available);
    }
}
