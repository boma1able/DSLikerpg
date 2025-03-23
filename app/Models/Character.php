<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    protected $fillable = [
        'user_id', 'race', 'avatar', 'class', 'level',
        'health', 'max_health', 'base_health', 'school_health_bonus', 'school_body_bonus',
        'body', 'base_body',
        'base_strength', 'strength', 'damage', 'base_damage', 'school_damage_bonus', 'school_strength_bonus',
        'mana', 'max_mana', 'base_max_mana',
        'agility', 'base_agility',
        'intelligence', 'base_intelligence',
        'experience',
        'armor', 'is_online', 'gold', 'skill_points',
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
        return $this->hasMany(School::class, 'character_id');
    }

}
