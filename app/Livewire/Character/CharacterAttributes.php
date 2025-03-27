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
    public $log = [];

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

        $this->updateCharacter();
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->skill_points = $this->character->skill_points;
    }

    public function loadBuffs()
    {
        $this->buffs = CharacterBuff::where('character_id', auth()->id())->get();

        $currentBuffsStatus = [];

        foreach ($this->buffs as $buff) {
            if ($buff->is_active != ($this->buffsActiveStatus[$buff->id] ?? 0)) {
                if ($buff->is_active) {
                    $this->addLogMessage("<span class='text-blue-700 font-semibold'>Бафф [{$buff->label}] застосовано!</span>");
                } else {
                    // Якщо баф завершився
                    $this->addLogMessage("<span class='text-blue-900 font-semibold'>Дія бафу [{$buff->label}] завершилась!</span>");
                }
            }

            $currentBuffsStatus[$buff->id] = $buff->is_active;
        }

        $this->buffsActiveStatus = $currentBuffsStatus;
    }

    public function addLogMessage($message)
    {
        $this->log[] = "{$message}";

        $this->dispatch('addLogMessage', $message);
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
