<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOwner;
use App\Models\Contracts\HasOwner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $phone
 * @property string|null $email
 * @property string|null $occupation
 * @property string|null $national_id
 * @property string|null $address
 * @property string|null $emergency_contact
 * @property int|null $family_members
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, Lease> $leases
 * @property-read int|null $leases_count
 *
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereFamilyMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Tenant extends Model implements HasOwner
{
    use BelongsToOwner, HasFactory;

    protected $fillable = [
        'user_id',
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

    public function ownerRelation(): ?string
    {
        return null;
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
}
