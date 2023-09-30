<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $user_ids = \DB::table('users')->select('id')->get();
        // $user_id = $faker->randomElement($user_ids)->id;
        return [
            'user_id' =>  User::all()->random()->id,
            'status' => fake()->boolean(),
        ];
    }
}
