<?php

namespace App\Livewire\V1\Rent;

use App\Data\RentLine;
use App\Enums\PaymentMethod;
use App\Models\Lease;
use App\Models\Payment;
use App\Services\RentService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rent')]
class RentRoll extends Component
{
    /** Month shown, as "YYYY-MM". Kept in the URL so it can be bookmarked. */
    #[Url]
    public string $month = '';

    /** Confirmation shown after a payment is recorded. */
    public string $notice = '';

    // The "Mark paid" pop-up
    public ?int $payingLeaseId = null;

    public $payAmount = '';

    public string $payMethod = '';

    public string $payDate = '';

    protected RentService $rent;

    public function boot(RentService $rent): void
    {
        $this->rent = $rent;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', Payment::class);

        $this->month = $this->period()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->notice = '';
        $this->month = $this->period()->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->notice = '';
        if ($this->canGoForward()) {
            $this->month = $this->period()->addMonth()->format('Y-m');
        }
    }

    /**
     * "Mark paid" opens a pop-up pre-filled with what's owed, the tenant's usual
     * payment method and today's date. The owner can change any of them.
     */
    public function openPayment(int $leaseId): void
    {
        Gate::authorize('create', Payment::class);

        // findOrFail goes through the owner scope: another user's lease is a 404.
        $lease = Lease::findOrFail($leaseId);

        $this->resetValidation();
        $this->payingLeaseId = $lease->id;
        $this->payAmount = $this->rent->lineFor($lease, $this->period())->balance();
        $this->payMethod = $this->rent->lastMethodFor($lease)->value;
        $this->payDate = now()->toDateString();
        unset($this->paying);
    }

    public function closePayment(): void
    {
        $this->payingLeaseId = null;
        $this->resetValidation();
        unset($this->paying);
    }

    public function confirmPayment(): void
    {
        Gate::authorize('create', Payment::class);

        $this->validate([
            'payingLeaseId' => 'required|integer',
            'payAmount' => 'required|numeric|min:0.01',
            'payMethod' => ['required', Rule::enum(PaymentMethod::class)],
            'payDate' => 'required|date|before_or_equal:today',
        ], [
            'payAmount.min' => 'Enter the amount received.',
            'payDate.before_or_equal' => 'The payment date can\'t be in the future.',
        ]);

        $lease = Lease::findOrFail($this->payingLeaseId);

        $payment = $this->rent->recordRent(
            $lease,
            $this->period(),
            (float) $this->payAmount,
            PaymentMethod::from($this->payMethod),
            CarbonImmutable::parse($this->payDate),
        );

        $this->notice = 'Recorded ৳'.number_format((float) $payment->amount_paid, 2)
            .' from '.$lease->tenant?->first_name.' for '.$this->period()->format('F Y').'.';

        $this->payingLeaseId = null;
        unset($this->lines, $this->summary, $this->paying);
    }

    /**
     * The rent line shown in the pop-up.
     */
    #[Computed]
    public function paying(): ?RentLine
    {
        $lease = $this->payingLeaseId ? Lease::with(['tenant', 'unit.property'])->find($this->payingLeaseId) : null;

        return $lease ? $this->rent->lineFor($lease, $this->period()) : null;
    }

    #[Computed]
    public function lines()
    {
        return $this->rent->rollFor($this->period());
    }

    #[Computed]
    public function summary(): array
    {
        return $this->rent->summaryFor($this->period());
    }

    public function canGoForward(): bool
    {
        // Allow recording rent one month in advance, not further.
        // startOfMonth() first: Jan 31 + 1 month would overflow into March.
        return $this->period()->lt(now()->startOfMonth()->addMonth());
    }

    /**
     * The month being shown. Falls back to the current month if the URL
     * holds something that isn't a valid "YYYY-MM".
     */
    public function period(): CarbonImmutable
    {
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $this->month) === 1) {
            return CarbonImmutable::createFromFormat('!Y-m', $this->month)->startOfMonth();
        }

        return CarbonImmutable::now()->startOfMonth();
    }

    public function render()
    {
        return view('livewire.v1.rent.rent-roll');
    }
}
