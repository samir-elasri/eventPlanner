<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::factory()->count(85)->create();
        $this->createAdditionalOverlappingEvents();
    }

    private function createAdditionalOverlappingEvents(): void
    {
        $startTime = Carbon::now()->addDays(1)->setTime(9, 0, 0);

        for ($i = 0; $i < 15; $i++) {
            $start = (clone $startTime)->addMinutes($i * 2);

            Event::create([
                'name' => fake()->sentence,
                'start_datetime' => $start,
                'duration' => fake()->numberBetween(120, 4320),
                'description' => fake()->paragraph,
                'location' => fake()->address,
                'capacity' => fake()->numberBetween(20, 400),
                'waitlist_capacity' => fake()->numberBetween(2, 100),
                'status' => fake()->randomElement(['draft', 'live']),
            ]);
        }
    }
}
