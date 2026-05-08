<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'first_name'        => $this->faker->firstName(),
            'last_name'         => $this->faker->lastName(),
            'email'             => $this->faker->unique()->safeEmail(),
            'phone'             => $this->faker->phoneNumber(),
            'occupation'        => $this->faker->jobTitle(),
            'national_id'       => $this->faker->numerify('##########'),
            'address'           => $this->faker->streetAddress() . ', ' . $this->faker->city(),
            'emergency_contact' => $this->faker->name() . ' (' . $this->faker->phoneNumber() . ')',
            'family_members'    => $this->faker->numberBetween(1, 5),
        ];
    }
}
