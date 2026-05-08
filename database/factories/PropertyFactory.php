<?php

namespace Database\Factories;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'property_name' => $this->faker->randomElement([
                'Sunrise Apartments',
                'Green Valley Residences',
                'The Urban Nest',
                'Blue Bay Towers',
                'Elm Street Flats',
                'Harmony Heights',
                'City View Complex',
            ]) . ' ' . $this->faker->buildingNumber(),
            'description'      => $this->faker->sentence(10),
            'property_type'    => $this->faker->randomElement(['apartment', 'house', 'commercial', 'duplex']),
            'address'          => $this->faker->streetAddress(),
            'city'             => $this->faker->randomElement(['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi']),
            'state'            => $this->faker->randomElement(['Dhaka Division', 'Chattogram Division', 'Sylhet Division']),
            'zip_code'         => $this->faker->postcode(),
            'country'          => 'Bangladesh',
            'status'           => PropertyStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => PropertyStatus::Inactive]);
    }
}
