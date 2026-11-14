<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentMethod;
use App\Models\Lease;
use App\Models\Payment;
use App\Rules\OwnedRecord;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PaymentFormObject extends Form
{
    public ?Payment $payment = null;

    public $lease_id = '';

    public string $payment_date = '';

    public string $rent_period = '';

    public $amount_paid = '';

    public string $payment_method = 'cash';

    public string $receipt_number = '';

    public string $notes = '';

    public bool $isEdit = false;

    public function rules(): array
    {
        return [
            'lease_id' => ['required', new OwnedRecord(Lease::class)],
            'payment_date' => 'required|date|before_or_equal:today',
            // Several payments per month are allowed (partial payments).
            'rent_period' => ['required', 'date'],
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => 'nullable|string|max:1000',
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
        $this->payment_method = $payment->payment_method->value;
        $this->notes = $payment->notes ?? '';
    }

    public function messages(): array
    {
        return [
            'amount_paid.min' => 'Please enter a valid payment amount greater than zero.',
            'lease_id.required' => 'You must select an active lease for this payment.',
        ];
    }
}
