<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColumnFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'order' => $this->faker->numberBetween(0, 10),
            'board_id' => Board::factory(),
        ];
    }
}
