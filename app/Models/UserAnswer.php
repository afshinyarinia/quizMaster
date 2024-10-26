<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['game_round_id', 'user_id', 'answer', 'is_correct'];

    public function gameRound()
    {
        return $this->belongsTo(GameRound::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
