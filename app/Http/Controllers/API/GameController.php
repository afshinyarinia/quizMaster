<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lobby;
use App\Services\GameService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function getCurrentQuestion(Lobby $lobby)
    {
        $currentRound = $lobby->gameRounds()->latest()->first();

        if (!$currentRound) {
            return response()->json(['error' => 'No active round'], 404);
        }

        return response()->json([
            'question' => $currentRound->question->content,
            'time_limit' => $currentRound->question->time_limit,
            'round_number' => $currentRound->round_number,
        ]);
    }

    public function submitAnswer(Request $request, Lobby $lobby)
    {
        $validatedData = $request->validate([
            'answer' => 'required|string',
        ]);

        $currentRound = $lobby->gameRounds()->latest()->first();

        if (!$currentRound) {
            return response()->json(['error' => 'No active round'], 404);
        }

        $this->gameService->submitAnswer($request->user(), $currentRound, $validatedData['answer']);

        return response()->json(['message' => 'Answer submitted successfully']);
    }

    public function getGameState(Lobby $lobby)
    {
        $activePlayers = $lobby->users()->wherePivot('is_active', true)->count();
        $currentRound = $lobby->gameRounds()->latest()->first();

        return response()->json([
            'status' => $lobby->status,
            'active_players' => $activePlayers,
            'current_round' => $currentRound ? $currentRound->round_number : null,
        ]);
    }
}
