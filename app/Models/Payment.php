<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $lease_id
 * @property string|null $receipt_number
 * @property \Illuminate\Support\Carbon $payment_date
 * @property \Illuminate\Support\Carbon $rent_period
 * @property numeric $amount_paid
 * @property PaymentMethod $payment_method
 * @property PaymentStatus $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lease|null $lease
 *
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereLeaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereRentPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

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

    protected static function booted(): void
    {
        if (auth()->check()) {
            static::addGlobalScope('user', function (\Illuminate\Database\Eloquent\Builder $builder) {
                $builder->whereHas('lease.unit.property', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            });
        }
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
