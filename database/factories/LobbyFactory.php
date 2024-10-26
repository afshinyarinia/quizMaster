<?php

namespace Database\Factories;

use App\Enums\LobbyStatus;
use App\Models\Lobby;
use Illuminate\Database\Eloquent\Factories\Factory;

class LobbyFactory extends Factory
{
    protected $model = Lobby::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'status' => $this->faker->randomElement(LobbyStatus::getValues()),
        ];
    }
}
