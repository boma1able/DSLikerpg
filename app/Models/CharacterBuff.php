<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterBuff extends Model
{
    protected $table = 'character_buffs';

    protected $fillable = [
        'buff_name', 'label', 'buff_amount', 'level', 'applied_at', 'is_active', 'duration',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'applied_at' => 'datetime',
    ];
}
