<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'property_name',
        'description',
        'property_type',
        'property_photo',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'status',
    ];

    protected $casts = [
        'status' => PropertyStatus::class,
    ];

    /**
     * Get the user that owns the property.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', \App\Enums\PropertyStatus::Active);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
