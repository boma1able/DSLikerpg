<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inventory extends Model
{
    protected $fillable = ['user_id', 'item_id', 'quantity', 'character_id'];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'inventory_item')
            ->withPivot('id', 'quantity', 'instance_id')
            ->withTimestamps();
    }
}


