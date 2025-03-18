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
            ['name' => 'Деревʼяний щит', 'image' => 'items/shields/wooden-shield.jpg', 'level' => '1', 'type' => 'shield', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 3],
            ['name' => 'Залізний шолом', 'image' => 'items/helmets/iron-helmet.jpg', 'level' => '1', 'type' => 'helmet', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 2],
            ['name' => 'Плащь з тряпок', 'image' => 'items/cloaks/ragged-cloak.jpg', 'level' => '1', 'type' => 'cloak', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 0.5],
            ['name' => 'Шкіряні перчатки', 'image' => 'items/gloves/leather-gloves.jpg', 'level' => '1', 'type' => 'gloves', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 1],
            ['name' => 'Залізна блоня', 'image' => 'items/chests/rusty-chest.jpg', 'level' => '1', 'type' => 'chest', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 10],
            ['name' => 'Простий пояс', 'image' => 'items/belts/simple-belt.jpg', 'level' => '1', 'type' => 'belt', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 0.5],
            ['name' => 'Шкіряні поножи', 'image' => 'items/leggings/leather-leggings.jpg', 'level' => '1', 'type' => 'leggings', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 2],
            ['name' => 'Шкіряні боти', 'image' => 'items/boots/leather-boots.jpg', 'level' => '1', 'type' => 'boots', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 1],
            ['name' => 'Залізне каблучка', 'image' => 'items/rings/iron-ring.jpg', 'level' => '1', 'type' => 'ring', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 0.2],
            ['name' => 'Залізний амулет', 'image' => 'items/amulets/iron-amulet.jpg', 'level' => '1', 'type' => 'amulet', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 0.2],
            ['name' => 'Залізний намисто', 'image' => 'items/necklaces/iron-necklace.jpg', 'level' => '1', 'type' => 'necklace', 'rarity' => 'common', 'stackable' => false, 'max_stack' => 1, 'weight' => 0.3],
            ['name' => 'Мале лікувальне зілля', 'image' => 'items/potions/health-potion.jpg', 'level' => '1', 'type' => 'potion', 'rarity' => 'uncommon', 'stackable' => true, 'max_stack' => 10, 'weight' => 0.05],
            ['name' => 'Щурячий хвіст', 'image' => 'items/ingredients/mouse-tail.jpg', 'level' => '1', 'type' => 'ingredient', 'rarity' => 'common', 'stackable' => true, 'max_stack' => 10, 'weight' => 0.05],
        ]);
    }
}
