<?php

namespace App\Models;

use App\Enums\LeaseStatus;
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
 * @property int $unit_id
 * @property int|null $tenant_id
 * @property string|null $document_path
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property numeric|null $security_deposit
 * @property numeric $rent_amount
 * @property numeric|null $service_charge
 * @property LeaseStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Tenant|null $tenant
 * @property-read Unit $unit
 *
 * @method static \Database\Factories\LeaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereSecurityDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereServiceCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lease whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Lease extends Model implements HasOwner
{
    use BelongsToOwner, HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'rent_amount',
        'service_charge',
        'security_deposit',
        'deposit_returned',
        'tenant_id',
        'unit_id',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => LeaseStatus::class,
    ];

    public function ownerRelation(): ?string
    {
        return 'unit.property';
    }

    /**
     * What the tenant owes each month: rent plus service charge.
     */
    public function monthlyCharge(): float
    {
        return round((float) $this->rent_amount + (float) ($this->service_charge ?? 0), 2);
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
