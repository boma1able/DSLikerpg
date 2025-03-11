<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monster extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'avatar', 'name', 'level', 'experience', 'position_x', 'position_y', 'health', 'damage', 'damage_min', 'damage_max', 'hit_chance'
    ];

    // Метод для атаки монстра
    public function attack(Character $character)
    {
        // Вираховуємо різницю в рівнях
        $levelDifference = $this->level - $character->level;

        // Початковий шанс попадання
        $hitChance = $this->hit_chance;

        // Якщо рівень монстра вищий за рівень персонажа
        if ($levelDifference > 0) {
            $hitChance += 0.05 * $levelDifference; // Збільшуємо шанс попадання на 5% за кожен рівень
        }
        // Якщо рівень персонажа вищий за рівень монстра
        elseif ($levelDifference < 0) {
            $hitChance -= 0.05 * abs($levelDifference); // Зменшуємо шанс попадання на 5% за кожен рівень
        }

        // Перевірка на максимальні та мінімальні межі шансів попадання
        $hitChance = max(0, min(1, $hitChance)); // Обмежуємо значення від 0 до 1

        // Перевірка на попадання
        if (rand(0, 100) / 100 <= $hitChance) {
            // Обчислення випадкового урону
            $damage = rand($this->damage_min, $this->damage_max);
            return $damage;
        }

        // Якщо не потрапили
        return 0;
    }
}
