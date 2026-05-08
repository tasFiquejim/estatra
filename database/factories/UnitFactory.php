<?php

namespace Database\Factories;

use App\Enums\UnitStatus;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'property_id'  => Property::factory(),
            'unit_name'    => 'Unit ' . $this->faker->randomElement(['A', 'B', 'C', 'D', '1A', '2B', '3C']),
            'floor_number' => $this->faker->numberBetween(1, 10),
            'size'         => $this->faker->randomElement([600, 750, 900, 1100, 1250, 1400]) . ' sqft',
            'notes'        => $this->faker->optional(0.4)->sentence(6),
            'status'       => UnitStatus::Available,
        ];
    }

    public function occupied(): static
    {
        return $this->state(['status' => UnitStatus::Occupied]);
    }

    public function available(): static
    {
        return $this->state(['status' => UnitStatus::Available]);
    }
}
