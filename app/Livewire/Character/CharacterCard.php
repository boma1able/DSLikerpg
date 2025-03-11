<?php

namespace App\Livewire\Character;

use Livewire\Component;

class CharacterCard extends Component
{
    public $character;
    public $experience;
    public $level;
    public $requiredExperience;

    public $healthBonus;
    public $manaBonus;
    public $damageBonus;
    public $armorBonus;

    public bool $isResting = false;

    protected $listeners = [
        'characterUpdated' => 'updateCharacter',
        'updateHealth' => 'handleupdateHealth',
        'restStarted' => 'handleRestingStatus',
        'stopResting' => 'handleRestingStatus',
    ];

    public function mount()
    {
        $this->character = auth()->user()->character;
        $this->experience = $this->character->experience;
        $this->level = $this->character->level;
        $this->requiredExperience = $this->getRequiredExperienceForLevel($this->level + 1);
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->experience = $this->character->experience;
        $this->level = $this->character->level;
        $this->requiredExperience = $this->getRequiredExperienceForLevel($this->level + 1);
    }

    private function getRequiredExperienceForLevel($level)
    {
        return (int) round(100 + ($level - 1) * 50 + pow(1.1, $level) * 20);
    }

    public function handleRestingStatus($eventData = null)
    {
        $this->isResting = $eventData['isResting'] ?? false;
    }

    public function updateRestingStatus($data)
    {
        $this->isResting = $data['isResting'];
    }

    public function handleupdateHealth($newHealth)
    {
        $this->character['health'] = $newHealth;
    }

    public function render()
    {
        return view('livewire.character.character-card');
    }
}

