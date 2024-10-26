<?php

use App\Models\GameRound;
use App\Models\Question;
use App\Models\User;
use App\Models\Lobby;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a lobby', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/lobbies', [
            'name' => 'Test Lobby',
        ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lobbies', ['name' => 'Test Lobby']);
});

it('can join a lobby', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $lobby = Lobby::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/lobbies/{$lobby->id}/join");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Joined lobby successfully']);
});

it('can start a lobby', function () {
    // there should be 2 users in the lobby to start the game
    $lobby = Lobby::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $lobby->users()->attach([$user1->id, $user2->id]);

    $token1 = $user1->createToken('auth_token')->plainTextToken;
    $token2 = $user2->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
        ->postJson("/api/lobbies/{$lobby->id}/start");

    $response->assertStatus(200);
});

it('can get the current question', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $lobby = Lobby::factory()->create();
    $question = Question::factory()->create();
    GameRound::factory()->create([
        'lobby_id' => $lobby->id,
        'question_id' => $question->id,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/lobbies/{$lobby->id}/question");

    $response->assertStatus(200)
        ->assertJsonStructure(['question', 'time_limit', 'round_number']);
});

it('can submit an answer', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $lobby = Lobby::factory()->create();
    $question = Question::factory()->create();
    GameRound::factory()->create([
        'lobby_id' => $lobby->id,
        'question_id' => $question->id,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/lobbies/{$lobby->id}/answer", [
            'answer' => 'Test Answer',
        ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Answer submitted successfully']);
});

it('can get the game state', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $lobby = Lobby::factory()->create();
    $question = Question::factory()->create();
    GameRound::factory()->create([
        'lobby_id' => $lobby->id,
        'question_id' => $question->id,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson("/api/lobbies/{$lobby->id}/state");

    $response->assertStatus(200)
        ->assertJsonStructure(['status', 'active_players', 'current_round']);
});
