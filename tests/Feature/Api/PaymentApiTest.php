<?php

namespace Tests\Feature\Api;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_payments_in_correct_json_format()
    {
        // 1. Arrange
        // Create exactly 20 fake payments in the database
        Payment::factory()->count(20)->create();
        // 2. Act
        // Make an HTTP GET request specifically requesting JSON
        $response = $this->getJson('/api/payments');
        // 3. Assert
        $response->assertStatus(200) // Expect a 200 OK
            ->assertJsonStructure([  // Strictly enforce the JSON shape
                'data' => [
                    '*' => [         // The '*' means check every single item in the array
                        'id',
                        'lease_id',
                        'amount_paid',
                        'receipt_number',
                        'payment_date',
                        'status',
                    ],
                ],
                'first_page_url',    // Assert pagination metadata exists
                'next_page_url',
                'total',
            ]);
        // Assert that the pagination works (we created 20, but page 1 should only have 15)
        $this->assertCount(15, $response->json('data'));
    }
}
