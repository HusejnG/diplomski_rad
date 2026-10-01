<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inverter>
 */
class InverterFactory extends Factory
{
    public function definition(): array
    {
        $rated = fake()->randomElement([3, 5, 6, 8, 10]);

        return [
            'manufacturer' => fake()->company(),
            'model' => strtoupper(fake()->bothify('INV-##??')),
            'type' => 'string',
            'rated_power_kw' => $rated,
            'max_pv_power_kw' => $rated * 1.5,
            'phases' => fake()->randomElement([1, 3]),
            'price_bam' => fake()->randomFloat(2, 1200, 4000),
            'warranty_years' => 10,
            'is_active' => true,
        ];
    }
}
