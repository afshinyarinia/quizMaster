<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\GameService;
use App\Models\User;
use App\Models\Lobby;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $gameService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gameService = new GameService();
    }

    public function testCreateLobby()
    {
        $lobby = $this->gameService->createLobby('Test Lobby');
        $this->assertInstanceOf(Lobby::class, $lobby);
        $this->assertEquals('Test Lobby', $lobby->name);
        $this->assertEquals('waiting', $lobby->status);
    }

    public function testJoinLobby()
    {
        $user = User::factory()->create();
        $lobby = Lobby::factory()->create();

        $this->gameService->joinLobby($user, $lobby);

        $this->assertTrue($lobby->users->contains($user));
    }

    public function testStartGame()
    {
        $lobby = Lobby::factory()->create();
        User::factory()->count(2)->create()->each(function ($user) use ($lobby) {
            $this->gameService->joinLobby($user, $lobby);
        });

        $this->gameService->startGame($lobby);

        $this->assertEquals('in_progress', $lobby->fresh()->status);
        $this->assertNotNull($lobby->gameRounds()->first());
    }
}
