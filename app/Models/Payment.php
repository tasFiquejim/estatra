<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'lease_id',
        'receipt_number',
        'payment_date',
        'rent_period',
        'amount_paid',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'rent_period' => 'date',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
