<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
    public function invoiceNumber(): Attribute
    {
        return Attribute::make(
            set: fn($value) => empty($value) ? null : $value,
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            set: fn($value) => empty($value) ? null : $value,
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
            'other' => 'Other'
        ];
    }
}
