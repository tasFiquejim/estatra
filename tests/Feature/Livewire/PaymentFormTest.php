<?php

namespace Tests\Feature\Livewire;

use App\Livewire\V1\Payment\PaymentForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_required_fields_before_saving_payment()
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(PaymentForm::class)
            ->set('form.amount_paid', -50)
            ->set('form.lease_id', '')
            ->call('save')

            ->assertHasErrors([
                'form.lease_id' => 'required',
                'form.amount_paid' => 'min',
            ]);
    }

    public function test_it_returns_custom_error_messages_for_bad_input()
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(PaymentForm::class)
            ->set('form.amount_paid', -10)
            ->set('form.lease_id', '')
            ->call('save')

            ->assertHasErrors([
                'form.amount_paid' => 'Please enter a valid payment amount greater than zero.',
                'form.lease_id' => 'You must select an active lease for this payment.',
            ]);
    }
}
