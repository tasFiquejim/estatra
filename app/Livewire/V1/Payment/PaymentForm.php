<?php

namespace App\Livewire\V1\Payment;

use Carbon\Carbon;
use App\Models\Lease;
use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

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
            'notes' => $payment->notes ?? '',
            'status' => $payment->status,
        ]);
    }
    public function title(): string
    {
        return $this->isEdit ? 'Edit Payment' : 'Add New Payment';
    }

    public function save()
    {
        if (!$this->isEdit && empty($this->receipt_number)) {
            $this->receipt_number = $this->generateReceptNo();
        }
        if (!empty($this->rent_period)) {
            $this->rent_period = Carbon::parse($this->rent_period)->startOfMonth()->format('Y-m-d');
        }
        $validated = $this->validate();

        if ($this->isEdit) {
            $this->payment->update($validated);
            $message = 'Payment record updated successfully!';
        } else {
            $validated['receipt_number'] = $this->receipt_number;
            Payment::create($validated);
            $message = 'Payment recorded successfully!';
        }
        session()->flash('success', $message);

        return $this->redirect(route('payment.index'));
    }
    private function generateReceptNo()
    {
        $lastNumber = Payment::whereNotNull('receipt_number')
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastNumber ? ((int)$lastNumber->receipt_number) + 1 : 1;

        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function cancel()
    {
        return $this->redirect(route('payment.index'));
    }
    public function  updatedLeaseId($value)
    {
        if ($value) {
            $lease = Lease::find($value);
            if ($lease) {

                $totalRent = $lease->rent_amount +  ($lease->service_charge ?? 0);
                $this->amount_paid = $totalRent;
            }
        }
    }
    #[Computed]
    public function activeLeases()
    {
        return Lease::with(['unit.property', 'tenant'])
            ->where('status', 'active')
            ->get()
            ->map(function ($lease) {
                return [
                    'id' => $lease->id,
                    'display' => $lease->unit->property->property_name . ' - Unit ' . $lease->unit->unit_name . ' (' . $lease->tenant->first_name . ' ' . $lease->tenant->last_name . ')',
                    // 'tenant_name' => $lease->tenant->first_name . ' ' . $lease->tenant->last_name,
                    // 'property_unit' => $lease->unit->property->property_name . ' - Unit ' . $lease->unit->unit_name,
                    // 'total_rent' => $lease->rent_amount + ($lease->service_charge ?? 0)
                ];
            });
    }

    #[Computed]
    public function selectedLease()
    {
        if (!$this->lease_id) {
            return null;
        }
        return Lease::with(['unit.property', 'tenant'])->find($this->lease_id);
    }

    public function render()
    {
        return view('livewire.v1.payment.payment-form');
    }
}
