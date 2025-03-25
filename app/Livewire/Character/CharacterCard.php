<?php

namespace App\Livewire\Character;

use App\Models\CharacterBuff;
use Livewire\Component;
use Livewire\Attributes\On;

class CharacterCard extends Component
{
    public $character;
    public $experience;
    public $level;
    public $skill_points;
    public $requiredExperience;
    public $buffsActiveStatus = [];
    public $buffs = [];


    public bool $isResting = false;

    protected $listeners = [
        'characterUpdated' => 'updateCharacter',
        'updateHealth' => 'handleupdateHealth',
        'restStarted' => 'handleRestingStatus',
        'stopResting' => 'handleRestingStatus',
        'schoolUpdated' => 'updateSchool',
        'updateBuffs' => 'setBuffs',
        'updateBuffsInCard' => 'loadBuffs',
    ];

    public function mount()
    {
        $this->updateCharacter();
        $this->updateSchool($this->character->max_health);

        $this->buffsActiveStatus = CharacterBuff::where('is_active', 1)
            ->pluck('is_active', 'id')
            ->toArray();
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

    public function setBuffs($buffs)
    {
        $this->buffs = collect($buffs)->map(fn($buff) => (object) $buff);
    }

    public function refreshBuffs()
    {
        $this->buffs = CharacterBuff::where('character_id', auth()->id())->get();
    }

    public function loadBuffs()
    {
        $this->buffs = CharacterBuff::where('character_id', auth()->id())->get();
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
        return view('livewire.character.character-card', [
            'buffs' => $this->buffs,
        ]);
    }
}

