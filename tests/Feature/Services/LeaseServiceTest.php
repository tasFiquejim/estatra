<?php

namespace Tests\Feature\Services;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Models\Lease;
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

    public function test_ending_a_lease_saves_the_status_frees_the_unit_and_sets_an_end_date(): void
    {
        $this->travelTo('2026-11-14');
        $lease = Lease::factory()->create(['end_date' => null]);

        (new LeaseService)->updateLease($lease, ['status' => LeaseStatus::Terminated->value]);

        $lease->refresh();
        // Before the fix "status" was missing from $fillable and silently ignored.
        $this->assertSame(LeaseStatus::Terminated, $lease->status);
        $this->assertSame('2026-11-14', $lease->end_date->toDateString());
        $this->assertSame(UnitStatus::Available, $lease->unit->status);
    }

    public function test_moving_a_tenant_to_another_unit_frees_the_old_one(): void
    {
        $lease = Lease::factory()->create();
        $oldUnit = $lease->unit;
        $newUnit = Unit::factory()->available()->create();

        (new LeaseService)->updateLease($lease, ['unit_id' => (string) $newUnit->id]);

        $this->assertSame(UnitStatus::Available, $oldUnit->fresh()->status);
        $this->assertSame(UnitStatus::Occupied, $newUnit->fresh()->status);
    }
}
