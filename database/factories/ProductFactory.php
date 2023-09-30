<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $category = ["men's clothing", "jewelery", "electronics", "women's clothing"];
        return [
            'name' =>  fake()->name(),
            'price' =>  rand(10, 500),
            'category' =>  $category[array_rand($category)],
            // 'category' =>  fake()->randomElement($category),
            'description' => fake()->text(100),
            'image' =>  fake()->imageUrl(),
        ];
    }
}
