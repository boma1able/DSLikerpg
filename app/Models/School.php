<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'character_schools';

    protected $fillable = ['character_id', 'school_name', 'level'];

    /**
     * Визначаємо зв'язок з моделлю Character
     */
    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    /**
     * Метод для перевірки, чи персонаж вже має цю школу
     */
    public static function getCharacterSchool($characterId, $schoolName)
    {
        return self::where('character_id', $characterId)
            ->where('school_name', $schoolName)
            ->first();
    }

    /**
     * Якщо потрібна методика для додавання бонусів при підвищенні рівня
     */
    public function applyLevelBonus()
    {
        // Напишіть ваші бонуси для кожної школи
        switch ($this->school_name) {
            case 'body':
                // Бонуси для школи "body"
                break;
            case 'strength':

                break;
            // Додати інші школи за потреби
        }
    }
}

