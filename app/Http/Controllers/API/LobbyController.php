<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Lobby;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class LobbyController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $lobby = $this->gameService->createLobby($validatedData['name']);

        return response()->json($lobby, 201);
    }

    public function join(Request $request, Lobby $lobby): ?JsonResponse
    {
        try {
            $this->gameService->joinLobby($request->user(), $lobby);
            return response()->json(['message' => 'Joined lobby successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function start(Lobby $lobby): JsonResponse
    {
        try {
            $this->gameService->startGame($lobby);
            return response()->json(['message' => 'Game started successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
