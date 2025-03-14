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

    protected $listeners = ['updateCharacterAttributes' => 'updateCharacterAttributes'];

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
        $healthPerLevel = 2;
        $baseHealth = (10 + pow($this->character['level'], 1.5) * 5) * 0.8;
        return $totalHealth = round($baseHealth * (1 + ($this->body * 0.2)) * $healthPerLevel);
    }

    public function calculateManaFromIntelligence()
    {
        $manaPerLevel = 1;
        $baseMana = (5 + pow($this->character['level'], 1.5) * 3) * 0.8;
        return round($baseMana * (1 + ($this->intelligence * 0.2)) * $manaPerLevel);
    }

    public function calculateDamageFromStrength()
    {
        $damagePerLevel = 0.5;
        $baseDamage = (2 + pow($this->character['level'], 1.5) * 1.5) * 0.8;
        return round($baseDamage * (1 + ($this->strength * 0.2)) * $damagePerLevel);
    }

    public function calculateArmorFromAgility()
    {
        $armorPerLevel = 1;
        $baseArmor = (1 + pow($this->character['level'], 1.5) * 1.2) * 0.8;
        return round($baseArmor * (1 + ($this->agility * 0.2)) * $armorPerLevel);
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

    public function closeModal()
    {
        $this->dispatch('closeStats');
    }

    public function render()
    {
        return view('livewire.character.character-attributes');
    }
}
