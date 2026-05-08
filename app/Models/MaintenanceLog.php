<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'product_cost'     => 'decimal:2',
        'service_cost'     => 'decimal:2',
        'total_cost'       => 'decimal:2',
        'status'           => MaintenanceStatus::class,
    ];

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
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->all();
    }

    public function calculateTotalCost()
    {
        return ($this->product_cost ?? 0) + ($this->service_cost ?? 0);
    }
}
