<?php

namespace Database\Factories;

use App\Enums\LeaseStatus;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaseFactory extends Factory
{
    protected $model = Lease::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-18 months', '-1 month');

        return [
            'unit_id'          => Unit::factory()->occupied(),
            'tenant_id'        => Tenant::factory(),
            'start_date'       => $startDate,
            'end_date'         => null, // open-ended by default
            'rent_amount'      => $this->faker->randomElement([8000, 10000, 12000, 15000, 18000, 20000]),
            'service_charge'   => $this->faker->randomElement([500, 1000, 1500, null]),
            'security_deposit' => $this->faker->randomElement([16000, 20000, 24000, 30000]),
            'status'           => LeaseStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => LeaseStatus::Active]);
    }

    public function expired(): static
    {
        return $this->state(function () {
            $start = $this->faker->dateTimeBetween('-24 months', '-13 months');
            $end   = $this->faker->dateTimeBetween('-12 months', '-1 month');

            return [
                'start_date' => $start,
                'end_date'   => $end,
                'status'     => LeaseStatus::Expired,
            ];
        });
    }
}
