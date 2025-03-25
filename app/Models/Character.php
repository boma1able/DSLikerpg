<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    protected $table = 'characters';

    protected $fillable = [
        'user_id', 'race', 'avatar', 'class', 'level',
        'health', 'max_health', 'base_health', 'school_health_bonus', 'school_body_bonus',
        'mana', 'base_mana', 'max_mana', 'school_mana_bonus',
        'body', 'base_body', 'school_body_bonus',
        'strength', 'base_strength', 'school_strength_bonus',
        'dexterity', 'base_dexterity', 'school_dexterity_bonus', 'base_hit_chance', 'hit_chance', 'school_hit_chance_bonus',
        'intelligence', 'base_intelligence', 'school_intelligence_bonus',
        'damage', 'base_damage', 'school_damage_bonus',
        'magic_damage', 'base_magic_damage', 'school_magic_damage_bonus', 'base_magic_hit_chance', 'magic_hit_chance', 'school_magic_hit_chance_bonus',
        'base_armor', 'armor', 'school_armor_bonus',
        'experience',
        'is_online', 'gold', 'skill_points',
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

    public function buffs()
    {
        return $this->hasMany(CharacterBuff::class, 'character_id');
    }
}
