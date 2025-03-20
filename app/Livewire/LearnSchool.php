<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Character;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class LearnSchool extends Component
{
    public $character;
    public $school;
    public int $maxLevels = 15;
    public $skill_points;

    protected $listeners = [
        'characterUpdated' => 'updateCharacter', //оновлює на фронті кількість скілпоінтів
    ];

    public function mount()
    {
        $this->character = Auth::user()->character; // Отримуємо персонажа авторизованого юзера

        // Отримуємо або створюємо школу
        $this->school = School::where('character_id', $this->character->id)
            ->where('school_name', 'body')
            ->first();

        if (!$this->school) {
            $this->school = School::create([
                'character_id' => $this->character->id,
                'school_name' => 'body',
                'level' => 0,
            ]);
        }

        $this->updateCharacter();
    }

    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->skill_points = $this->character->skill_points;
    }

    public function learnLevel()
    {
        if ($this->school->level < $this->maxLevels && $this->character->skill_points > 0) {
            $this->character->decrement('skill_points');
            $this->character->increment('max_health', 5);
            $this->school->increment('level');

            // Оновлюємо дані
            $this->character = $this->character->refresh();
            $this->school = $this->school->refresh();
        }

        $this->dispatch('schoolUpdated', $this->character->max_health);
    }

    public function render()
    {
        return view('livewire.learn-school');
    }
}

