<?php

namespace App\Enums;

enum LobbyStatus: string
{
    case WAITING = 'waiting';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
