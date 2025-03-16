<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'level', 'type', 'rarity', 'stackable', 'max_stack', 'weight'];

}

