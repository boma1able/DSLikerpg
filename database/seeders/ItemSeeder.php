<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        Item::insert([
            ['name' => 'Залізний меч', 'image' => 'items/swords/icon-sword.jpg', 'level' => '1', 'type' => 'weapon', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 5],
            ['name' => 'Лікувальне зілля', 'image' => 'items/potions/health-potion.jpg', 'level' => '1', 'type' => 'potion', 'rarity' => 'uncommon', 'stackable' => true, 'max_stack' => 10, 'weight' => 0.5],
        ]);
    }
}
