<?php

namespace App\Jobs;

use App\Models\GameRound;
use App\Services\GameService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// app/Jobs/ProcessGameRound.php
class ProcessGameRound implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gameRound;

    public function __construct(GameRound $gameRound)
    {
        $this->gameRound = $gameRound;
    }

    public function handle(GameService $gameService)
    {
        $gameService->processRound($this->gameRound);
    }
}
