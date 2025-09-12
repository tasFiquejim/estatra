<?php

namespace App\Livewire\Payment;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]

#[Title('Payments')]
class PaymentForm extends Component
{
    public ?Payment $payment = null;

    public string $lease_id = '';
    public string $payment_date = '';
    public string $rent_period = '';
    public string $amount_paid = '';
    public string $payment_method = 'cash';
    public string $receipt_number = '';
    public string $notes = '';
    public string $status = 'paid';

    public bool $isEdit = false;
    protected function rules(): array

    {
        $rules = [
            'lease_id' => 'required|exists:leases,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'rent_period' => [
                'required',
                'date',
                Rule::unique('payments', 'rent_period')
                    ->where('lease_id', $this->lease_id)
                    ->ignore($this->isEdit ? $this->payment->id : null)
            ],
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_payment,cheque',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:paid,unpaid,partial',
        ];

        return $rules;
    }
    public function mount(Payment $payment): void
    {
        $this->payment_date = now()->format('Y-m-d');
        $this->rent_period = now()->startOfMonth()->format('Y-m-d');
        $this->payment = $payment;

        if ($payment && $payment->exists) {
            $this->initializeForEdit($payment);
        }
    }

    public function initializeForEdit(Payment $payment): void
    {
        $this->isEdit = true;

        $this->fill([
            'lease_id' => $payment->lease_id,
            'payment_date' => $payment->payment_date->format('Y-m-d'),
            'rent_period' => $payment->rent_period->format('Y-m-d'),
            'amount_paid' => $payment->amount_paid,
            'payment_method' => $payment->payment_method,
            'receipt_number' => $payment->receipt_number ?? '',
            'notes' => $payment->notes ?? '',
            'status' => $payment->status,
        ]);
    }
    public function title(): string
    {
        return $this->isEdit ? 'Edit Payment' : 'Add New Payment';
    }

    public function render()
    {
        return view('livewire.payment.payment-form');
    }
}
