<?php

namespace App\Livewire\Character;

use Livewire\Component;

class CharacterSkills extends Component
{

    public $character;
    public $skill_points;

    protected $listeners = [
        'characterUpdated' => 'updateCharacter', //оновлює на фронті кількість скілпоінтів
    ];

    public function mount()
    {
        $this->updateCharacter();
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->skill_points = $this->character->skill_points;
    }

    public function render()
    {
        return view('livewire.character.character-skills');
    }
}
