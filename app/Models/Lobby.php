<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lobby extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'lobby_users');
    }

    public function gameRounds()
    {
        return $this->hasMany(GameRound::class);
    }
}
