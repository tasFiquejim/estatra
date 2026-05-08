<?php

namespace Database\Factories;

use App\Models\MaintenanceLog;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceLogFactory extends Factory
{
    protected $model = MaintenanceLog::class;

    public function definition(): array
    {
        $productCost = $this->faker->optional(0.7)->randomElement([200, 500, 800, 1500, 3000]);
        $serviceCost = $this->faker->optional(0.8)->randomElement([300, 500, 1000, 2000]);
        $totalCost   = ($productCost ?? 0) + ($serviceCost ?? 0);

        return [
            'property_id'       => Property::factory(),
            'unit_id'           => null,
            'maintenance_date'  => $this->faker->dateTimeBetween('-3 months', 'now'),
            'issue_description' => $this->faker->randomElement([
                'Leaking pipe in bathroom',
                'Electrical socket not working in bedroom',
                'Air conditioner service required',
                'Roof seepage during rain',
                'Paint peeling in living room',
                'Broken door lock',
                'Water heater not working',
                'Tiles cracked in kitchen',
            ]),
            'product_cost'  => $productCost,
            'service_cost'  => $serviceCost,
            'total_cost'    => $totalCost > 0 ? $totalCost : null,
            'status'        => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'on_hold']),
            'photo'         => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
