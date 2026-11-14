<?php

namespace Tests\Feature\Livewire;

use App\Livewire\V1\Unit\UnitForm;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnitFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_unit_with_empty_optional_fields_can_be_edited(): void
    {
        $owner = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $owner->id]);
        $unit = Unit::factory()->for($property)->create(['floor_number' => null, 'size' => null, 'notes' => null]);

        $this->actingAs($owner)
            ->get(route('unit.edit', [$property, $unit]))
            ->assertOk();

        Livewire::actingAs($owner)
            ->test(UnitForm::class, ['property' => $property, 'unit' => $unit])
            ->set('unit_name', 'A-101')
            ->call('save')
            ->assertHasNoErrors();

        $unit->refresh();
        $this->assertSame('A-101', $unit->unit_name);
        $this->assertNull($unit->size);
        $this->assertNull($unit->notes);
    }
}
