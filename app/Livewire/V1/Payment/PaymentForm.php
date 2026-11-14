<?php

namespace App\Livewire\V1\Payment;

use App\Livewire\Forms\PaymentFormObject;
use App\Models\Lease;
use App\Models\Payment;
use App\Services\LeaseService;
use App\Services\PaymentService;
use App\Services\RentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Payments')]
class PaymentForm extends Component
{
    public PaymentFormObject $form;

    // protected: services are not sent to the browser. Livewire refills
    // them on every request through boot(), which supports injection.
    protected LeaseService $leases;

    public function boot(LeaseService $leases): void
    {
        $this->leases = $leases;
    }

    public function mount(Payment $payment, RentService $rent): void
    {
        $this->form->payment_date = now()->format('Y-m-d');
        $this->form->rent_period = now()->format('Y-m');

        if ($payment->exists) {
            Gate::authorize('update', $payment);
            $this->form->setPayment($payment);
            $this->form->rent_period = $payment->rent_period->format('Y-m');

            return;
        }

        Gate::authorize('create', Payment::class);

        // Opened from the Rent screen: ?lease=12&period=2026-11
        $lease = Lease::find(request()->integer('lease'));

        if ($lease !== null) {
            $period = request()->filled('period')
                ? Carbon::parse(request()->string('period').'-01')
                : now();

            $this->form->lease_id = $lease->id;
            $this->form->rent_period = $period->format('Y-m');
            $this->form->amount_paid = $rent->lineFor($lease, $period)->balance();
        }
    }

    public function title(): string
    {
        return $this->form->isEdit ? 'Edit Payment' : 'Add New Payment';
    }

    public function save(PaymentService $paymentService)
    {
        if (! empty($this->form->rent_period)) {
            $this->form->rent_period = Carbon::parse($this->form->rent_period)->startOfMonth()->format('Y-m-d');
        }
        $validated = $this->form->validate();

        if (! $this->form->isEdit) {
            $validated['receipt_number'] = $this->form->receipt_number;
        }

        try {
            if ($this->form->isEdit) {
                $paymentService->updatePayment($this->form->payment, $validated);
                $message = 'Payment record updated successfully!';
            } else {
                $paymentService->recordPayment($validated);
                $message = 'Payment recorded successfully!';
            }
            session()->flash('success', $message);

            return $this->redirect(route('payment.index'));
        } catch (\Exception $e) {
            report($e);
            $this->addError('form.lease_id', 'An error occurred while saving the payment.');
        }
    }

    public function cancel()
    {
        return $this->redirect(route('payment.index'));
    }

    public function updatedFormLeaseId($value): void
    {
        $lease = $value ? Lease::find($value) : null;

        if ($lease !== null) {
            $this->form->amount_paid = $this->leases->calculateTotalRent($lease);
        }
    }

    #[Computed]
    public function activeLeases(): array
    {
        return $this->leases->activeLeaseOptions();
    }

    #[Computed]
    public function selectedLease()
    {
        if (! $this->form->lease_id) {
            return null;
        }

        return Lease::with(['unit.property', 'tenant'])->find($this->form->lease_id);
    }

    public function render()
    {
        return view('livewire.v1.payment.payment-form');
    }
}
