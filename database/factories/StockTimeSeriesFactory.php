<?php

namespace Database\Factories;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTimeSeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => Stock::factory(),
            'timestamp' => fake()->dateTime,
            'open' => fake()->randomFloat(4),
            'high' => fake()->randomFloat(4),
            'low' => fake()->randomFloat(4),
            'close' => fake()->randomFloat(4),
            'volume' => fake()->numberBetween(1, 200),
        ];
    }
}
