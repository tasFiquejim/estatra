<?php

namespace App\Models;

use App\Enums\RepairStatus;
use App\Models\Concerns\BelongsToOwner;
use App\Models\Contracts\HasOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $property_id
 * @property Carbon $expense_date
 * @property string $category
 * @property numeric $amount
 * @property string|null $invoice_number
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Property $property
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereExpenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyExpense whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PropertyExpense extends Model implements HasOwner
{
    use BelongsToOwner, HasFactory;

    public const REPAIR = 'maintenance';

    protected $fillable = [
        'property_id',
        'unit_id',
        'expense_date',
        'category',
        'repair_status',
        'amount',
        'invoice_number',
        'description',
        'photo',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'repair_status' => RepairStatus::class,
    ];

    public function ownerRelation(): ?string
    {
        return 'property';
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function isRepair(): bool
    {
        return $this->category === self::REPAIR;
    }

    /**
     * Repairs that still need fixing.
     *
     * @param  Builder<self>  $query
     */
    public function scopeOpenRepairs(Builder $query): void
    {
        $query->where('category', self::REPAIR)->where('repair_status', RepairStatus::Open);
    }

    public function invoiceNumber(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => empty($value) ? null : $value,
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => empty($value) ? null : $value,
        );
    }

    public static function getCategories()
    {
        return [
            self::REPAIR => 'Repairs & Maintenance',
            'utilities' => 'Utilities',
            'cleaning' => 'Cleaning',
            'security' => 'Security',
            'insurance' => 'Insurance',
            'taxes' => 'Property Taxes',
            'water' => 'Water Bill',
            'electricity' => 'Electricity Bill',
            'other' => 'Other',
        ];
    }
}
