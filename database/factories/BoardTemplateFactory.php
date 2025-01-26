<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardTemplateFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'team_id' => Team::factory(),
            'columns' => [
                ['name' => 'What went well?'],
                ['name' => 'What could be improved?'],
                ['name' => 'Action items'],
            ]
        ];
    }

    public function forTeam(Team $team)
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }
}
