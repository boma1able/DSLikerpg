<?php

namespace App\Livewire\Character;

use App\Models\Character;
use Livewire\Component;

class CharacterAttributes extends Component
{
    public $character, $body, $strength, $agility, $intelligence, $damage;
    public $skill_points;

    protected $listeners = [
        'characterUpdated' => 'updateCharacter',
        'schoolUpdated' => 'updateSchool',
    ];

    public function mount(Character $character)
    {
        $this->character = $character;
        $this->body = $character->body;
        $this->strength = $character->strength;
        $this->agility = $character->agility;
        $this->intelligence = $character->intelligence;

        // Оновлюємо максимальне здоров'я і поточне здоров'я
        $this->updateCharacter();
        $this->updateSchool(
            $this->character->max_health,
            $this->character->damage,
        );
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->skill_points = $this->character->skill_points;
    }

    public function updateSchool($totalHealth, $totalDamage)
    {
        // Оновлюємо атрибути персонажа
        $this->character->max_health = $totalHealth;
        $this->character->damage = $totalDamage;
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
