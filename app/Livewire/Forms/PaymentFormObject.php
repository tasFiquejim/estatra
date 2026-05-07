<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PaymentFormObject extends Form
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

    public function rules(): array
    {
        return [
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
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => 'nullable|string|max:1000',
            'status' => ['required', Rule::enum(PaymentStatus::class)],
        ];
    }

    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
        $this->isEdit = true;

        $this->lease_id = $payment->lease_id;
        $this->payment_date = $payment->payment_date->format('Y-m-d');
        $this->rent_period = $payment->rent_period->format('Y-m-d');
        $this->amount_paid = $payment->amount_paid;
        $this->payment_method = $payment->payment_method?->value ?? 'cash';
        $this->notes = $payment->notes ?? '';
        $this->status = $payment->status?->value ?? 'paid';
    }
}
