<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CreateLobbyRequest;
use App\Http\Resources\API\LobbyResource;
use App\Models\Lobby;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\LobbyException;
use App\Http\Responses\ApiResponse;

class LobbyController extends Controller
{
    /**
     * @var GameService
     */
    protected GameService $gameService;

    /**
     * LobbyController constructor.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Create a new lobby.
     *
     * @param CreateLobbyRequest $request
     * @return JsonResponse
     */
    public function create(CreateLobbyRequest $request): JsonResponse
    {
        try {
            $lobby = $this->gameService->createLobby($request->validated('name'));

            return ApiResponse::success(
                new LobbyResource($lobby),
                'Lobby created successfully',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to create lobby',
                500,
                $e
            );
        }
    }

    /**
     * Join a lobby.
     *
     * @param Request $request
     * @param Lobby $lobby
     * @return JsonResponse
     */
    public function join(Request $request, Lobby $lobby): JsonResponse
    {
        try {
            $this->gameService->joinLobby($request->user(), $lobby);

            return ApiResponse::success(
                null,
                'Joined lobby successfully'
            );
        } catch (LobbyException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                400
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to join lobby',
                500,
                $e
            );
        }
    }

    /**
     * Start a game in the lobby.
     *
     * @param Lobby $lobby
     * @return JsonResponse
     */
    public function start(Lobby $lobby): JsonResponse
    {
        try {
            $this->gameService->startGame($lobby);

            return ApiResponse::success(
                null,
                'Game started successfully'
            );
        } catch (LobbyException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                400
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to start game',
                500,
                $e
            );
        }
    }
}
