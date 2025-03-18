<?php

namespace Database\Seeders;

use App\Models\Monster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonstersTableSeeder extends Seeder
{
    public function run(): void
    {
        $monsters = [
            ['name' => 'Rat', 'avatar' => 'rat.jpg', 'health' => 10, 'damage' => 1, 'level' => 1, 'experience' => 10, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 1, 'gold_max' => 3],
//            ['name' => 'Cat', 'avatar' => 'cat.jpg', 'health' => 15, 'damage' => 3, 'level' => 2, 'experience' => 15, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 3, 'gold_max' => 7],
//            ['name' => 'Dog', 'avatar' => 'dog.jpg', 'health' => 20, 'damage' => 5, 'level' => 3, 'experience' => 25, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 7, 'gold_max' => 10],
//            ['name' => 'Beggar', 'avatar' => 'beggar.jpg', 'health' => 25, 'damage' => 6, 'level' => 4, 'experience' => 30, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 10, 'gold_max' => 14],
//            ['name' => 'Bandit', 'avatar' => 'bandit.jpg', 'health' => 30, 'damage' => 7, 'level' => 5, 'experience' => 45, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 13, 'gold_max' => 17],
//            ['name' => 'Thief', 'avatar' => 'thief.jpg', 'health' => 25, 'damage' => 9, 'level' => 7, 'experience' => 60, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 15, 'gold_max' => 20],
//            ['name' => 'Guard', 'avatar' => 'guard.jpg', 'health' => 40, 'damage' => 12, 'level' => 9, 'experience' => 70, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 18, 'gold_max' => 23],
//            ['name' => 'Knight', 'avatar' => 'knight.jpg', 'health' => 60, 'damage' => 15, 'level' => 10, 'experience' => 80, 'position_x' => 2, 'position_y' => 3, 'hit_chance' => 0.75, 'gold_min' => 21, 'gold_max' => 26],
        ];

        $now = now();

        foreach ($monsters as $monster) {
            Monster::create(array_merge($monster, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

}
