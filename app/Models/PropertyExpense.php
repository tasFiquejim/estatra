<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property \Illuminate\Support\Carbon $expense_date
 * @property string $category
 * @property numeric $amount
 * @property string|null $invoice_number
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Property $property
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
class PropertyExpense extends Model
{
    protected $fillable = [
        'property_id',
        'expense_date',
        'category',
        'amount',
        'invoice_number',
        'description',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
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
            'maintenance' => 'Maintenance & Repairs',
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
