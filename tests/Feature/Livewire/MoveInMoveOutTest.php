<?php

namespace Tests\Feature\Livewire;

use App\Enums\LeaseStatus;
use App\Enums\UnitStatus;
use App\Livewire\V1\Lease\MoveIn;
use App\Livewire\V1\Lease\MoveOut;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\RentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MoveInMoveOutTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Property $property;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-11-15 10:00:00');
        $this->owner = User::factory()->create();
        $this->property = Property::factory()->create(['user_id' => $this->owner->id]);
        $this->unit = Unit::factory()->available()->for($this->property)->create(['unit_name' => 'A-101']);
    }

    private function moveIn()
    {
        return Livewire::actingAs($this->owner)->test(MoveIn::class, ['property' => $this->property, 'unit' => $this->unit]);
    }

    public function test_a_new_tenant_moves_in_with_one_form(): void
    {
        $this->moveIn()
            ->set('first_name', 'Rahim')
            ->set('phone', '01700000000')
            ->set('rent_amount', 12000)
            ->set('security_deposit', 24000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('property.show', $this->property));

        $tenant = Tenant::sole();
        $this->assertSame('Rahim', $tenant->first_name);
        $this->assertSame($this->owner->id, $tenant->user_id);

        $lease = Lease::sole();
        $this->assertTrue($lease->tenant->is($tenant));
        $this->assertSame(LeaseStatus::Active, $lease->status);
        $this->assertSame('2026-11-15', $lease->start_date->toDateString());
        $this->assertSame(UnitStatus::Occupied, $this->unit->fresh()->status);

        // ...and their rent shows up straight away on the Rent screen.
        $this->actingAs($this->owner);
        $this->assertSame(12000.0, app(RentService::class)->summaryFor(now())['outstanding']);
    }

    public function test_an_existing_tenant_can_move_in(): void
    {
        $tenant = Tenant::factory()->create(['user_id' => $this->owner->id]);

        $this->moveIn()
            ->set('tenantMode', 'existing')
            ->set('tenant_id', $tenant->id)
            ->set('rent_amount', 9000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, Tenant::count());
        $this->assertTrue(Lease::sole()->tenant->is($tenant));
    }

    public function test_someone_elses_tenant_cannot_be_used(): void
    {
        $theirs = Tenant::factory()->create();

        $this->moveIn()
            ->set('tenantMode', 'existing')
            ->set('tenant_id', $theirs->id)
            ->set('rent_amount', 9000)
            ->call('save')
            ->assertHasErrors(['tenant_id']);

        $this->assertSame(0, Lease::count());
    }

    public function test_rent_is_prefilled_from_the_last_tenant(): void
    {
        Lease::factory()->for($this->unit)->create([
            'rent_amount' => 15000, 'service_charge' => 1000, 'status' => LeaseStatus::Expired, 'end_date' => '2026-10-31',
        ]);
        $this->unit->update(['status' => UnitStatus::Available]);

        $this->moveIn()
            ->assertSet('rent_amount', '15000.00')
            ->assertSet('service_charge', '1000.00');
    }

    public function test_nobody_can_move_into_an_occupied_unit(): void
    {
        $this->unit->update(['status' => UnitStatus::Occupied]);

        $this->moveIn()->assertRedirect(route('property.show', $this->property));
    }

    public function test_the_unit_must_belong_to_the_property_in_the_url(): void
    {
        $otherProperty = Property::factory()->create(['user_id' => $this->owner->id]);

        $this->actingAs($this->owner)
            ->get(route('unit.move-in', [$otherProperty, $this->unit]))
            ->assertNotFound();
    }

    public function test_an_owner_cannot_open_move_in_for_someone_elses_unit(): void
    {
        $theirUnit = Unit::factory()->available()->create();

        $this->actingAs($this->owner)
            ->get(route('unit.move-in', [$theirUnit->property_id, $theirUnit]))
            ->assertNotFound();
    }

    private function activeLease(array $attributes = []): Lease
    {
        $this->unit->update(['status' => UnitStatus::Occupied]);

        return Lease::factory()->for($this->unit)->create(array_merge([
            'tenant_id' => Tenant::factory()->create(['user_id' => $this->owner->id])->id,
            'rent_amount' => 10000,
            'service_charge' => null,
            'security_deposit' => 20000,
            'start_date' => '2026-09-01',
            'end_date' => null,
            'status' => LeaseStatus::Active,
        ], $attributes));
    }

    public function test_move_out_shows_unpaid_rent_and_suggests_what_to_return(): void
    {
        $lease = $this->activeLease();
        Payment::factory()->create(['lease_id' => $lease->id, 'amount_paid' => 10000, 'rent_period' => '2026-09-01']);
        Payment::factory()->create(['lease_id' => $lease->id, 'amount_paid' => 4000, 'rent_period' => '2026-10-01']);

        // October: 6000 short, November: 10000 unpaid → 16000 owed, 20000 - 16000 = 4000 back.
        Livewire::actingAs($this->owner)
            ->test(MoveOut::class, ['lease' => $lease])
            ->assertSee('October 2026')
            ->assertSee('November 2026')
            ->assertDontSee('September 2026')
            ->assertSet('deposit_returned', 4000.0)
            // Moving out at the end of October: November isn't owed.
            ->set('move_out_date', '2026-10-31')
            ->assertSet('deposit_returned', 14000.0);
    }

    public function test_moving_out_ends_the_lease_and_frees_the_unit(): void
    {
        $lease = $this->activeLease();

        Livewire::actingAs($this->owner)
            ->test(MoveOut::class, ['lease' => $lease])
            ->set('move_out_date', '2026-11-20')
            ->set('deposit_returned', 15000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('property.show', $this->property));

        $lease->refresh();
        $this->assertSame(LeaseStatus::Terminated, $lease->status);
        $this->assertSame('2026-11-20', $lease->end_date->toDateString());
        $this->assertEquals(15000, $lease->deposit_returned);
        $this->assertSame(UnitStatus::Available, $this->unit->fresh()->status);
    }

    public function test_leaving_at_the_planned_end_counts_as_expired(): void
    {
        $lease = $this->activeLease(['end_date' => '2026-11-30']);

        Livewire::actingAs($this->owner)
            ->test(MoveOut::class, ['lease' => $lease])
            ->set('move_out_date', '2026-11-30')
            ->call('save');

        $this->assertSame(LeaseStatus::Expired, $lease->fresh()->status);
    }

    public function test_cannot_return_more_than_the_deposit_or_leave_before_moving_in(): void
    {
        $lease = $this->activeLease();

        Livewire::actingAs($this->owner)
            ->test(MoveOut::class, ['lease' => $lease])
            // Date first: changing the date re-suggests the deposit amount.
            ->set('move_out_date', '2026-08-01')
            ->set('deposit_returned', 25000)
            ->call('save')
            ->assertHasErrors(['deposit_returned' => 'max', 'move_out_date' => 'after_or_equal']);

        $this->assertSame(LeaseStatus::Active, $lease->fresh()->status);
    }

    public function test_the_old_leases_screen_is_gone(): void
    {
        $this->actingAs($this->owner)->get('/v1/leases')->assertNotFound();
        $this->actingAs($this->owner)->get('/v1/maintenances')->assertNotFound();
    }

    public function test_the_property_page_offers_move_in_and_move_out(): void
    {
        $occupied = Unit::factory()->occupied()->for($this->property)->create(['unit_name' => 'B-202']);
        $lease = Lease::factory()->for($occupied)->create([
            'tenant_id' => Tenant::factory()->create(['user_id' => $this->owner->id, 'first_name' => 'Karim'])->id,
        ]);

        $this->actingAs($this->owner)
            ->get(route('property.show', $this->property))
            ->assertOk()
            ->assertSee(route('unit.move-in', [$this->property, $this->unit]))
            ->assertSee(route('lease.move-out', $lease))
            ->assertSee('Karim');
    }
}
