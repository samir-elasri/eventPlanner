<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence,
            'start_datetime' => fake()->dateTimeBetween('now', '+30 days'),
            'duration' => fake()->numberBetween(120, 4320),
            'description' => fake()->paragraph,
            'location' => fake()->address,
            'capacity' => fake()->numberBetween(20, 400),
            'waitlist_capacity' => fake()->numberBetween(2, 100),
            'status' => fake()->randomElement(['draft', 'live']),
        ];
    }
}
