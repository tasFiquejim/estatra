<?php

namespace App\Livewire\V1\Payment;

use App\Models\Lease;
use App\Models\Payment;
use App\Livewire\Forms\PaymentFormObject;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.app')]
#[Title('Payments')]
class PaymentForm extends Component
{
    public PaymentFormObject $form;

    public function mount(Payment $payment): void
    {
        $this->form->payment_date = now()->format('Y-m-d');
        $this->form->rent_period = now()->startOfMonth()->format('Y-m-d');

        if ($payment && $payment->exists) {
            Gate::authorize('update', $payment);
            $this->form->setPayment($payment);
        } else {
            Gate::authorize('create', Payment::class);
        }
    }

    public function title(): string
    {
        return $this->form->isEdit ? 'Edit Payment' : 'Add New Payment';
    }

    public function save(\App\Services\PaymentService $paymentService)
    {
        if (!empty($this->form->rent_period)) {
            $this->form->rent_period = Carbon::parse($this->form->rent_period)->startOfMonth()->format('Y-m-d');
        }
        $validated = $this->form->validate();

        if (!$this->form->isEdit) {
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
            $this->addError('form.lease_id', 'An error occurred while saving the payment.');
        }
    }

    public function cancel()
    {
        return $this->redirect(route('payment.index'));
    }

    public function updatedFormLeaseId($value)
    {
        if ($value) {
            $lease = Lease::find($value);
            if ($lease) {
                $totalRent = $lease->rent_amount +  ($lease->service_charge ?? 0);
                $this->form->amount_paid = $totalRent;
            }
        }
    }

    #[Computed]
    public function activeLeases()
    {
        return Lease::with(['unit.property', 'tenant'])
            ->whereHas('unit.property', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('status', \App\Enums\LeaseStatus::Active)
            ->get()
            ->map(function ($lease) {
                return [
                    'id' => $lease->id,
                    'display' => $lease->unit->property->property_name . ' - Unit ' . $lease->unit->unit_name . ' (' . $lease->tenant->first_name . ' ' . $lease->tenant->last_name . ')',
                ];
            });
    }

    #[Computed]
    public function selectedLease()
    {
        if (!$this->form->lease_id) {
            return null;
        }
        return Lease::with(['unit.property', 'tenant'])->find($this->form->lease_id);
    }

    public function render()
    {
        return view('livewire.v1.payment.payment-form');
    }
}
