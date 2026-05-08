<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyExpenseFactory extends Factory
{
    protected $model = PropertyExpense::class;

    private static int $invoiceCounter = 1;

    public function definition(): array
    {
        return [
            'property_id'    => Property::factory(),
            'expense_date'   => $this->faker->dateTimeBetween('-3 months', 'now'),
            'category'       => $this->faker->randomElement(array_keys(PropertyExpense::getCategories())),
            'amount'         => $this->faker->randomElement([500, 800, 1200, 1500, 2000, 3500, 5000]),
            'invoice_number' => str_pad(self::$invoiceCounter++, 4, '0', STR_PAD_LEFT),
            'description'    => $this->faker->optional(0.6)->sentence(6),
        ];
    }
}
