<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    protected $fillable = [
        'user_id', 'race', 'avatar', 'class', 'level', 'health', 'max_health', 'mana', 'max_mana', 'experience',
        'damage', 'armor', 'is_online', 'gold', 'body', 'strength', 'agility', 'intelligence', 'skill_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function equipment()
    {
        return $this->hasOne(Equipment::class);
    }

    public function schools()
    {
        return $this->hasMany(School::class, 'character_id'); // Вказуємо кастомну назву таблиці
    }

}
