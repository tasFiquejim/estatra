<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Concerns\BelongsToOwner;
use App\Models\Contracts\HasOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $lease_id
 * @property string|null $receipt_number
 * @property Carbon $payment_date
 * @property Carbon $rent_period
 * @property numeric $amount_paid
 * @property PaymentMethod $payment_method
 * @property PaymentStatus $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Lease|null $lease
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
class Payment extends Model implements HasOwner
{
    use BelongsToOwner, HasFactory;

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

    public function ownerRelation(): ?string
    {
        return 'lease.unit.property';
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
