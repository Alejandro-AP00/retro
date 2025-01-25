<?php

namespace Database\Factories;

use App\Models\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReplyFactory extends Factory
{
    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'column_id' => Column::factory(),
        ];
    }
}
