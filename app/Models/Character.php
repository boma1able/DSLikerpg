<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    protected $fillable = [
        'user_id', 'race', 'avatar', 'class', 'level', 'health', 'mana', 'experience',
        'damage', 'is_online', 'gold', 'body', 'strength', 'agility', 'intelligence'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
