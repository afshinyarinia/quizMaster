<?php
namespace App\Services;

use App\Events\GameEvent;
use App\Models\GameRound;
use App\Models\Lobby;
use App\Models\Question;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Cache;

class GameService
{
    public function createLobby(string $name): Lobby
    {
        return Lobby::create([
            'name' => $name,
            'status' => 'waiting',
        ]);
    }

    public function joinLobby(User $user, Lobby $lobby): void
    {
        if ($lobby->users()->count() >= 64) {
            throw new \Exception('Lobby is full');
        }

        $lobby->users()->attach($user->id);
    }

    public function startGame(Lobby $lobby): void
    {
        if ($lobby->users()->count() < 2) {
            throw new \Exception('Not enough players to start the game');
        }

        $lobby->update(['status' => 'in_progress']);
        $this->createNextRound($lobby);
        broadcast(new GameEvent($lobby->id, 'game_started', [
            'message' => 'The game has started!',
        ]));
    }

    public function createNextRound(Lobby $lobby): ?GameRound
    {
        $nextRoundNumber = $lobby->gameRounds()->count() + 1;
        $question = Question::inRandomOrder()->first();

        if (!$question) {
            $this->endGame($lobby);
            return null;
        }
        broadcast(new GameEvent($lobby->id, 'new_round', [
            'question' => $question->content,
            'time_limit' => $question->time_limit,
            'round_number' => $nextRoundNumber,
        ]));
        return GameRound::create([
            'lobby_id' => $lobby->id,
            'question_id' => $question->id,
            'round_number' => $nextRoundNumber,
            'start_time' => now(),
        ]);
    }

    public function submitAnswer(User $user, GameRound $gameRound, string $answer): void
    {
        $isCorrect = $answer === $gameRound->question->correct_answer;

        UserAnswer::create([
            'game_round_id' => $gameRound->id,
            'user_id' => $user->id,
            'answer' => $answer,
            'is_correct' => $isCorrect,
        ]);

        if (!$isCorrect) {
            $lobby = $gameRound->lobby;
            $lobby->users()->updateExistingPivot($user->id, ['is_active' => false]);
        }
    }

    public function endRound(GameRound $gameRound): void
    {
        $gameRound->update(['end_time' => now()]);

        $lobby = $gameRound->lobby;
        $activePlayersCount = $lobby->users()->wherePivot('is_active', true)->count();

        broadcast(new GameEvent($gameRound->lobby_id, 'round_ended', [
            'round_number' => $gameRound->round_number,
            'correct_answer' => $gameRound->question->correct_answer,
        ]));
        if ($activePlayersCount <= 1) {
            $this->endGame($lobby);
        } else {
            $this->createNextRound($lobby);
        }
    }

    public function endGame(Lobby $lobby): void
    {
        $lobby->update(['status' => 'finished']);
        broadcast(new GameEvent($lobby->id, 'game_ended', [
            'message' => 'The game has ended!',
            // Include winner information and final scores
        ]));
        // Determine winner and update leaderboard
    }

    public function getGameState(Lobby $lobby)
    {
        return Cache::remember("game_state:{$lobby->id}", 60, function () use ($lobby) {
            $activePlayers = $lobby->users()->wherePivot('is_active', true)->count();
            $currentRound = $lobby->gameRounds()->latest()->first();

            return [
                'status' => $lobby->status,
                'active_players' => $activePlayers,
                'current_round' => $currentRound ? $currentRound->round_number : null,
            ];
        });
    }

    public function invalidateGameStateCache(Lobby $lobby): void
    {
        Cache::forget("game_state:{$lobby->id}");
    }
}
