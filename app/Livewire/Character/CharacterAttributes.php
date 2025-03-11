<?php

namespace App\Livewire\Character;

use App\Models\Character;
use Livewire\Component;

class CharacterAttributes extends Component
{
    public $character;
    public $body;
    public $strength;
    public $agility;
    public $intelligence;

    public function mount($character)
    {
        // Ініціалізуємо атрибути з переданого персонажа
        $this->character = $character;
        $this->body = $character['body'];
        $this->strength = $character['strength'];
        $this->agility = $character['agility'];
        $this->intelligence = $character['intelligence'];
    }

    // Функції для розрахунку бонусів на рівень
    public function calculateHealthFromBody()
    {
        $healthPerLevel = 3; // Кожен пункт body дає 3 HP на рівень
        return round((10 + ($this->character['level'] * 5)) * 0.8) * $this->body * $healthPerLevel;
    }

    public function calculateManaFromIntelligence()
    {
        $manaPerLevel = 2; // Кожен пункт intelligence дає 2 мани на рівень
        return round((5 + ($this->character['level'] * 3)) * 0.8) * $this->intelligence * $manaPerLevel;
    }

    public function calculateDamageFromStrength()
    {
        $damagePerLevel = 1; // Кожен пункт strength дає 1 шкоди на рівень
        return round((2 + round($this->character['level'] * 1.5)) * 0.8) * $this->strength * $damagePerLevel;
    }

    public function calculateArmorFromAgility()
    {
        $armorPerLevel = 1.5; // Кожен пункт agility дає 1.5 броні на рівень
        return round($this->character['level'] * 1.5) * 0.8 * $this->agility * $armorPerLevel;
    }

    // Оновлення персонажа з новими значеннями атрибутів
    public function updateCharacterAttributes()
    {
        // Оновлюємо значення на основі формул
        $this->character['max_health'] = $this->calculateHealthFromBody();
        $this->character['max_mana'] = $this->calculateManaFromIntelligence();
        $this->character['damage'] = $this->calculateDamageFromStrength();
        $this->character['armor'] = $this->calculateArmorFromAgility();

        // Оновлюємо атрибути персонажа
        $this->character['body'] = $this->body;
        $this->character['strength'] = $this->strength;
        $this->character['agility'] = $this->agility;
        $this->character['intelligence'] = $this->intelligence;

        // Збереження оновлених даних в базі
        $character = Character::find($this->character['id']);

        if (!$character) {
            return;
        }

        $character->body = $this->character['body'];
        $character->strength = $this->character['strength'];
        $character->agility = $this->character['agility'];
        $character->intelligence = $this->character['intelligence'];
        $character->max_health = $this->character['max_health'];
        $character->max_mana = $this->character['max_mana'];
        $character->damage = $this->character['damage'];
        $character->armor = $this->character['armor'];

        $character->save();

        // Оновлюємо локальні дані після збереження в базі
        $this->character = $character->toArray();

        $this->dispatch('characterUpdated', character: $this->character);

    }

    public function increment($attribute)
    {
        if (property_exists($this, $attribute)) {
            $this->$attribute++;
            $this->updateCharacterAttributes();
        }
    }

    public function decrement($attribute)
    {
        if (property_exists($this, $attribute) && $this->$attribute > 0) {
            $this->$attribute--;
            $this->updateCharacterAttributes();
        }
    }


    public function render()
    {
        return view('livewire.character.character-attributes');
    }
}
