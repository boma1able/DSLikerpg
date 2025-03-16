<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{

    protected $fillable = [
        'user_id',
        'character_id',
        'item_id',
        'slot'
    ];

    // Відношення до предмета
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Відношення до користувача
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Відношення до персонажа
    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
