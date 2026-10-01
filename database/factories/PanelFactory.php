<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Panel>
 */
class PanelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'model' => strtoupper(fake()->bothify('PV-###??')),
            'technology' => 'monokristalni',
            'power_w' => fake()->numberBetween(400, 550),
            'efficiency_percent' => fake()->randomFloat(2, 19, 22.5),
            'length_mm' => 1900,
            'width_mm' => 1100,
            'price_bam' => fake()->randomFloat(2, 250, 400),
            'warranty_years' => 12,
            'is_active' => true,
        ];
    }
}
