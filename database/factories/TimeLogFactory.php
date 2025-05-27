<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeLog>
 */
class TimeLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $start = $this->faker->dateTimeBetween('-10 days', 'now');
    $end = (clone $start)->modify('+'.rand(1, 4).' hours');

    return [
        'project_id' => Project::factory(),
        'start_time' => $start,
        'end_time' => $end,
        'description' => $this->faker->sentence(),
        'hours' => round(($end->getTimestamp() - $start->getTimestamp()) / 3600, 2),
        'tag' => $this->faker->randomElement(['billable', 'non-billable']),
    ];
    }
}
