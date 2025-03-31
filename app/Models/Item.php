<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'image', 'description', 'level', 'type', 'rarity', 'stackable', 'max_stack', 'weight',  'instance_id', 'properties'];

    protected $casts = [
        'properties' => 'array',
    ];

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_item')->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inventoryItem) {
            if (empty($inventoryItem->instance_id)) {
                $inventoryItem->instance_id = Str::uuid(); // Генерація унікального UUID
            }
        });
    }

}

