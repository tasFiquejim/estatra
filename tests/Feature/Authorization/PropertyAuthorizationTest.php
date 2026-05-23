<?php

namespace Tests\Feature\Authorization;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_edit_his_own_property()
    {
        $this->withoutVite();

        $userA = $this->authenticateUser();

        $property = Property::factory()->create([
            'user_id' => $userA->id,
        ]);

        $response = $this->get(route('property.edit', $property->id));

        $response->assertSuccessful();
    }

    public function test_user_cannot_edit_another_users_property()
    {
        $userA = User::factory()->create();
        $this->authenticateUser();

        $property = Property::factory()->create([
            'user_id' => $userA->id,
        ]);

        $response = $this->get(route('property.edit', $property->id));

        $response->assertNotfound();
    }
}
