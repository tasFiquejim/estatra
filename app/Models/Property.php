<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Models\Concerns\BelongsToOwner;
use App\Models\Contracts\HasOwner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $property_name
 * @property string|null $description
 * @property string|null $property_type
 * @property string|null $property_photo
 * @property string $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $country
 * @property PropertyStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Unit> $units
 * @property-read int|null $units_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property active()
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePropertyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePropertyPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePropertyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereZipCode($value)
 *
 * @mixin \Eloquent
 */
class Property extends Model implements HasOwner
{
    use BelongsToOwner, HasFactory;

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

    public function ownerRelation(): ?string
    {
        return null;
    }

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
        return $query->where('status', PropertyStatus::Active);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
