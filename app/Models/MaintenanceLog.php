<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property int|null $unit_id
 * @property \Illuminate\Support\Carbon $maintenance_date
 * @property string $issue_description
 * @property numeric|null $product_cost
 * @property numeric|null $service_cost
 * @property numeric|null $total_cost
 * @property MaintenanceStatus $status
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Property $property
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereIssueDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereMaintenanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereProductCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereServiceCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MaintenanceLog extends Model
{
    protected $fillable = [
        'property_id',
        'unit_id',
        'maintenance_date',
        'issue_description',
        'product_cost',
        'service_cost',
        'total_cost',
        'status',
        'photo',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'product_cost' => 'decimal:2',
        'service_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'status' => MaintenanceStatus::class,
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

    // Mutators for nullable fields
    public function setUnitIdAttribute($value)
    {
        $this->attributes['unit_id'] = empty($value) ? null : $value;
    }

    public function setProductCostAttribute($value)
    {
        $this->attributes['product_cost'] = empty($value) ? null : $value;
    }

    public function setServiceCostAttribute($value)
    {
        $this->attributes['service_cost'] = empty($value) ? null : $value;
    }

    public function setTotalCostAttribute($value)
    {
        $this->attributes['total_cost'] = empty($value) ? null : $value;
    }

    public function setPhotoAttribute($value)
    {
        $this->attributes['photo'] = empty($value) ? null : $value;
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Use MaintenanceStatus::cases() to get all statuses
    /** @deprecated Use MaintenanceStatus enum directly */
    public static function getStatuses(): array
    {
        return collect(MaintenanceStatus::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->all();
    }

    public function calculateTotalCost()
    {
        return ($this->product_cost ?? 0) + ($this->service_cost ?? 0);
    }
}
