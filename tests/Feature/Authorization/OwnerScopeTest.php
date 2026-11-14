<?php

namespace Tests\Feature\Authorization;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_owners_only_see_their_own_records_even_if_models_were_used_before_login(): void
    {
        // Using the models before anyone logs in "boots" them. The old scope was
        // only added if someone was logged in at boot time, so here it was never
        // added and every owner saw everyone's data.
        $someoneElses = Payment::factory()->create();
        $this->assertSame(1, Payment::count());

        $owner = User::factory()->create();
        $mine = Payment::factory()->for(Lease::factory()->ownedBy($owner))->create();

        $this->actingAs($owner);

        $this->assertSame([$mine->id], Payment::pluck('id')->all());
        $this->assertSame([$mine->lease_id], Lease::pluck('id')->all());
        $this->assertSame([$mine->lease->unit_id], Unit::pluck('id')->all());
        $this->assertSame([$mine->lease->tenant_id], Tenant::pluck('id')->all());
        $this->assertSame(1, Property::count());
        $this->assertNull(Payment::find($someoneElses->id));
    }

    public function test_without_a_logged_in_user_nothing_is_filtered(): void
    {
        // Queue workers and console commands have no user; they must still see records.
        Payment::factory()->count(2)->create();

        $this->assertSame(2, Payment::count());
    }
}
