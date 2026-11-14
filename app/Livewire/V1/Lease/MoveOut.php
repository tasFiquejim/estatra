<?php

namespace App\Livewire\V1\Lease;

use App\Data\RentLine;
use App\Enums\LeaseStatus;
use App\Models\Lease;
use App\Services\LeaseService;
use App\Services\RentService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * "Move out": ends the lease, frees the unit, and settles the deposit,
 * showing any rent still owed so the owner knows what to keep back.
 *
 * @property-read Collection<int, RentLine> $unpaid
 */
#[Layout('layouts.app')]
#[Title('Move out')]
class MoveOut extends Component
{
    public Lease $lease;

    public string $move_out_date = '';

    public $deposit_returned = '';

    protected RentService $rent;

    public function boot(RentService $rent): void
    {
        $this->rent = $rent;
    }

    public function mount(Lease $lease)
    {
        Gate::authorize('update', $lease);

        $this->lease = $lease->load(['tenant', 'unit.property']);

        if ($lease->status !== LeaseStatus::Active) {
            session()->flash('success', ($lease->tenant->first_name ?? 'This tenant').' has already moved out.');

            return $this->redirect(route('property.show', $lease->unit->property));
        }

        $this->move_out_date = now()->toDateString();
        $this->deposit_returned = $this->suggestedReturn();
    }

    public function updatedMoveOutDate(): void
    {
        unset($this->unpaid);
        $this->deposit_returned = $this->suggestedReturn();
    }

    protected function rules(): array
    {
        return [
            'move_out_date' => ['required', 'date', 'after_or_equal:'.$this->lease->start_date->toDateString()],
            'deposit_returned' => ['nullable', 'numeric', 'min:0', 'max:'.$this->deposit()],
        ];
    }

    protected function messages(): array
    {
        return [
            'move_out_date.after_or_equal' => 'The move-out date can\'t be before the tenant moved in.',
            'deposit_returned.max' => 'You can\'t return more than the deposit of ৳'.number_format($this->deposit(), 2).'.',
        ];
    }

    public function save(LeaseService $leases)
    {
        $data = $this->validate();

        try {
            $leases->moveOut(
                $this->lease,
                CarbonImmutable::parse($data['move_out_date']),
                $data['deposit_returned'] === '' || $data['deposit_returned'] === null ? null : (float) $data['deposit_returned'],
            );
        } catch (Exception $e) {
            $this->addError('move_out_date', $e->getMessage());

            return;
        }

        session()->flash('success', $this->lease->tenant?->first_name.' moved out. Unit '.$this->lease->unit->unit_name.' is free again.');

        return $this->redirect(route('property.show', $this->lease->unit->property));
    }

    /**
     * Months with rent still owed, up to the move-out month.
     *
     * @return Collection<int, RentLine>
     */
    #[Computed]
    public function unpaid(): Collection
    {
        $until = rescue(fn () => CarbonImmutable::parse($this->move_out_date), now(), report: false);

        return $this->rent->unpaidMonths($this->lease, $until);
    }

    public function unpaidTotal(): float
    {
        return round($this->unpaid->sum(fn ($line) => $line->balance()), 2);
    }

    public function deposit(): float
    {
        return (float) ($this->lease->security_deposit ?? 0);
    }

    /** Give back the deposit minus any unpaid rent (never below zero). */
    public function suggestedReturn(): float
    {
        return max(0.0, round($this->deposit() - $this->unpaidTotal(), 2));
    }

    public function render()
    {
        return view('livewire.v1.lease.move-out');
    }
}
