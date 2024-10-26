<?php

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
