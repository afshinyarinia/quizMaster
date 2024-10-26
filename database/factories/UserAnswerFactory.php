<?php

namespace Database\Factories;

use App\Models\GameRound;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserAnswerFactory extends Factory
{
    protected $model = UserAnswer::class;

    public function definition(): array
    {
        return [
            'answer' => $this->faker->word(),
            'is_correct' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'game_round_id' => GameRound::factory(),
            'user_id' => User::factory(),
        ];
    }
}
