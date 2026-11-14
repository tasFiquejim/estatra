<?php

namespace App\Models;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Models\Concerns\BelongsToOwner;
use App\Models\Contracts\HasOwner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $property_id
 * @property string $unit_name
 * @property string|null $floor_number
 * @property float|null $size
 * @property string|null $notes
 * @property UnitStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Lease> $leases
 * @property-read int|null $leases_count
 * @property-read Property $property
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
class Unit extends Model implements HasOwner
{
    use BelongsToOwner, HasFactory;

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

    public function ownerRelation(): ?string
    {
        return 'property';
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return HasMany<Lease, $this>
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * The lease of whoever lives here now (the newest active one).
     */
    public function activeLease(): HasOne
    {
        return $this->hasOne(Lease::class)->ofMany(
            ['id' => 'max'],
            fn ($query) => $query->where('status', LeaseStatus::Active),
        );
    }

    public function scopeisAvailable($query)
    {
        return $query->where('status', UnitStatus::Available);
    }
}
