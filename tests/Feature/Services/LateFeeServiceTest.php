<?php

namespace Tests\Feature\Services;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\LateFeeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LateFeeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_charges_no_late_fee_if_paid_within_grace_period()
    {
        // 1. Arrange: Create our data
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);
        $unit = Unit::factory()->create(['property_id' => $property->id]);
        $tenant = Tenant::factory()->create();
        
        $lease = Lease::factory()->create([
            'unit_id' => $unit->id,
            'tenant_id' => $tenant->id,
            'rent_amount' => 1000,
        ]);

        $service = new LateFeeService();
        
        // Assume rent period is January 2026. Payment made on Jan 4th.
        $paymentDate = Carbon::parse('2026-01-04'); 
        $rentPeriod = Carbon::parse('2026-01-01');

        // 2. Act: Call the unbuilt feature
        $lateFee = $service->calculate($lease, $rentPeriod, $paymentDate);

        // 3. Assert: The late fee should be $0 because it's before the 5th
        $this->assertEquals(0, $lateFee);
    }

    public function test_it_charges_ten_percent_late_fee_if_paid_after_grace_period()
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);
        $unit = Unit::factory()->create(['property_id' => $property->id]);
        $tenant = Tenant::factory()->create();
        
        $lease = Lease::factory()->create([
            'unit_id' => $unit->id,
            'tenant_id' => $tenant->id,
            'rent_amount' => 1000, // 10% of 1000 is 100
        ]);

        $service = new LateFeeService();
        
        // Payment made on Jan 6th (after the 5-day grace period)
        $paymentDate = Carbon::parse('2026-01-06'); 
        $rentPeriod = Carbon::parse('2026-01-01');

        $lateFee = $service->calculate($lease, $rentPeriod, $paymentDate);

        // The late fee should be $100
        $this->assertEquals(100, $lateFee);
    }
}
