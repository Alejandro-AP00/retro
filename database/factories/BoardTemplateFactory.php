<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BoardTemplateFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'columns' => [
                ['name' => 'What went well?'],
                ['name' => 'What could be improved?'],
                ['name' => 'Action items'],
            ]
        ];
    }
}
