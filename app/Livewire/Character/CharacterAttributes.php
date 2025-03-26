<?php

namespace App\Livewire\Character;

use App\Models\Character;
use App\Models\CharacterBuff;
use Livewire\Component;

class CharacterAttributes extends Component
{
    public $character, $body, $strength, $dexterity, $intelligence, $damage, $magic_damage, $armor;
    public $skill_points;
    public $buffs = [];
    public $buffsActiveStatus = [];

    protected $listeners = [
        'characterUpdated' => 'updateCharacter',
        'schoolUpdated' => 'updateSchool',
        'updateBuffsInCard' => 'loadBuffs',
    ];

    public function mount(Character $character)
    {
        $this->character = $character;
        $this->body = $character->body;
        $this->strength = $character->strength;
        $this->dexterity = $character->dexterity;
        $this->intelligence = $character->intelligence;

        $this->buffsActiveStatus = CharacterBuff::where('is_active', 1)
            ->pluck('is_active', 'id')
            ->toArray();

        // Оновлюємо максимальне здоров'я і поточне здоров'я
        $this->updateCharacter();
//        $this->updateSchool(
//            $this->character->max_health,
//            $this->character->max_mana,
//            $this->character->damage,
//            $this->character->magic_damage,
//            $this->character->armor,
//        );
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->skill_points = $this->character->skill_points;
    }

//    public function updateSchool($totalHealth, $totalDamage, $totalMagicDamage, $totalArmor, $totalMana)
//    {
//        // Оновлюємо атрибути персонажа
//        $this->character->max_health = $totalHealth;
//        $this->character->max_mana = $totalMana;
//        $this->character->damage = $totalDamage;
//        $this->character->magic_damage = $totalMagicDamage;
//        $this->character->armor = $totalArmor;
//    }

    public function loadBuffs()
    {
        $this->buffs = CharacterBuff::where('character_id', auth()->id())->get();
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
