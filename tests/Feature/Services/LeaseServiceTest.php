<?php

namespace Tests\Feature\Services;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\LeaseService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_an_exception_if_unit_is_not_available(): void
    {
        $unit = Unit::factory()->occupied()->create();
        $tenant = Tenant::factory()->create();
        $service = new LeaseService;

        $leaseData = [
            'unit_id' => $unit->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->format('Y-m-d'),
            'rent_amount' => 1000,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This unit is not available for lease.');

        $service->createLease($leaseData);
    }

    public function test_it_creates_a_lease_and_updates_unit_status(): void
    {
        $unit = Unit::factory()->available()->create();
        $tenant = Tenant::factory()->create();
        $service = new LeaseService;

        $leaseData = [
            'unit_id' => $unit->id,
            'tenant_id' => $tenant->id,
            'start_date' => now()->format('Y-m-d'),
            'rent_amount' => 1000,
            'security_deposit' => 2000,
            'status' => LeaseStatus::Active,
        ];

        $lease = $service->createLease($leaseData);

        $this->assertNotNull($lease->id);

        $this->assertEquals(UnitStatus::Occupied, $unit->fresh()->status);
    }
}
