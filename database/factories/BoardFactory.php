<?php

namespace Database\Factories;

use App\Models\BoardTemplate;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'owner_id' => User::factory(),
            'team_id' => Team::factory(),
            'board_template_id' => BoardTemplate::factory(),
            'locked_at' => $this->faker->boolean(20) ? $this->faker->dateTimeBetween('-1 week', '+1 week') : null,
        ];
    }

    public function forTeam(Team $team)
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }
}
