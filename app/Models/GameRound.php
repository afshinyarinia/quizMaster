<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    use HasFactory;

    protected $fillable = ['lobby_id', 'question_id', 'round_number', 'start_time', 'end_time'];

    public function lobby()
    {
        return $this->belongsTo(Lobby::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
