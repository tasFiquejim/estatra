<?php

namespace App\Models;

use App\Enums\LeaseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $unit_id
 * @property int|null $tenant_id
 * @property string|null $document_path
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property numeric|null $security_deposit
 * @property numeric $rent_amount
 * @property numeric|null $service_charge
 * @property LeaseStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Tenant|null $tenant
 * @property-read \App\Models\Unit $unit
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
class Lease extends Model
{
    use HasFactory;

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
