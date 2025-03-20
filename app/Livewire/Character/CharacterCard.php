<?php

namespace App\Livewire\Character;

use Livewire\Component;

class CharacterCard extends Component
{
    public $character;
    public $experience;
    public $level;
    public $skill_points;
    public $requiredExperience;

    public bool $isResting = false;

    protected $listeners = [
        'characterUpdated' => 'updateCharacter',
        'updateHealth' => 'handleupdateHealth',
        'restStarted' => 'handleRestingStatus',
        'stopResting' => 'handleRestingStatus',
        'schoolUpdated' => 'updateSchool',
    ];

    public function mount()
    {
        $this->updateCharacter();
        $this->updateSchool($this->character->max_health);
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->experience = $this->character->experience;
        $this->level = $this->character->level;
        $this->skill_points = $this->character->skill_points;
        $this->requiredExperience = $this->getRequiredExperienceForLevel($this->level + 1);
    }

    public function updateSchool($newMaxHealth)
    {
        $this->character->max_health = $newMaxHealth;
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

