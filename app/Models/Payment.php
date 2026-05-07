<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Lease;
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
        'rent_period' => 'date:Y-m',
        'status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
