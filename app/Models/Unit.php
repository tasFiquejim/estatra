<?php

namespace App\Models;

use App\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $property_id
 * @property string $unit_name
 * @property string|null $floor_number
 * @property float|null $size
 * @property string|null $notes
 * @property UnitStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lease> $leases
 * @property-read int|null $leases_count
 * @property-read \App\Models\Property $property
 *
 * @method static \Database\Factories\UnitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit isAvailable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereFloorNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Unit extends Model
{
    use HasFactory;

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

    protected static function booted(): void
    {
        if (auth()->check()) {
            static::addGlobalScope('user', function (\Illuminate\Database\Eloquent\Builder $builder) {
                $builder->whereHas('property', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            });
        }
    }

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
