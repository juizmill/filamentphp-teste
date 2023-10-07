<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'description' => $this->faker->text,
            'ean' => $this->faker->numberBetween(10000000000, 99999999999),
            'sku' => 'EC' .  (string) $this->faker->unique()->numberBetween(1000, 9999),
            'model' => $this->faker->words(3, true),
            'quantity' => $this->faker->numberBetween(1, 100),
            'category_id' => Category::query()->inRandomOrder()->first()->id,
            'brand_id' => Brand::query()->inRandomOrder()->first()->id,
        ];
    }
}
