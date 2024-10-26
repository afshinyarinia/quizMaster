<?php

namespace Database\Factories;

use App\Models\GameRound;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameRoundFactory extends Factory
{
    protected $model = GameRound::class;

    public function definition(): array
    {
        return [
            'lobby_id' => 1,
            'question_id' => 1,
            'round_number' => 1,
            'start_time' => now(),
            'end_time' => now(),
        ];
    }
}
