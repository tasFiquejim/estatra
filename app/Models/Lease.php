<?php

namespace App\Models;

use App\Enums\LeaseStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'rent_amount',
        'service_charge',
        'security_deposit',
        'tenant_id',
        'unit_id',
        'property_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => LeaseStatus::class,
    ];

    protected static function booted(): void
    {
        if (auth()->check()) {
            static::addGlobalScope('user', function (\Illuminate\Database\Eloquent\Builder $builder) {
                $builder->whereHas('unit.property', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            });
        }
    }
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ?: null;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }   
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
