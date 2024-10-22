<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'correct_answer', 'time_limit'];

    public function gameRounds()
    {
        return $this->hasMany(GameRound::class);
    }
}
