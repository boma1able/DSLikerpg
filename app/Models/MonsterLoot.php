<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonsterLoot extends Model
{
    use HasFactory;

    protected $table = 'monster_loot';

    protected $fillable = ['monster_id', 'item_id', 'drop_chance'];

    public function monster()
    {
        return $this->belongsTo(Monster::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
