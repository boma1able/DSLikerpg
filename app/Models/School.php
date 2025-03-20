<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'character_schools';

    protected $fillable = ['character_id', 'school_name', 'level'];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
