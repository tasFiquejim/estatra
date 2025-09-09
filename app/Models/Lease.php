<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lease extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'rent_amount',
        'security_deposit',
        'tenant_id',
        'unit_id',
        'property_id',
        'payment_frequency',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }   
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
