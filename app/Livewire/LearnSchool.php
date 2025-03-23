<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Character;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class LearnSchool extends Component
{
    public $character;
    public int $maxLevels = 15;
    public $skill_points;
    public $selectedSchool = 'body';
    public $school;
    public $schools = ['body', 'strength'];

    public $schoolLevels = [
        'body' => [
            1 => ['body' => 1, 'max_health' => 5],
            2 => ['body' => 1, 'max_health' => 5],
            3 => ['body' => 1, 'max_health' => 5],
            4 => ['body' => 1, 'max_health' => 5],
            5 => ['body' => 1, 'max_health' => 10],
            6 => ['body' => 1, 'max_health' => 5],
            7 => ['body' => 1, 'max_health' => 5],
            8 => ['body' => 1, 'max_health' => 5],
            9 => ['body' => 1, 'max_health' => 5],
            10 => ['body' => 1, 'max_health' => 10],
            11 => ['body' => 1, 'max_health' => 5],
            12 => ['body' => 1, 'max_health' => 5],
            13 => ['body' => 1, 'max_health' => 5],
            14 => ['body' => 1, 'max_health' => 5],
            15 => ['body' => 2, 'max_health' => 20],
        ],
        'strength' => [
            1 => ['strength' => 1, 'damage' => 1],
            2 => ['strength' => 1, 'damage' => 1],
            3 => ['strength' => 1, 'damage' => 1],
            4 => ['strength' => 1, 'damage' => 1],
            5 => ['strength' => 1, 'damage' => 3],
            6 => ['strength' => 1, 'damage' => 1],
            7 => ['strength' => 1, 'damage' => 1],
            8 => ['strength' => 1, 'damage' => 1],
            9 => ['strength' => 1, 'damage' => 1],
            10 => ['strength' => 1, 'damage' => 3],
            11 => ['strength' => 1, 'damage' => 1],
            12 => ['strength' => 1, 'damage' => 1],
            13 => ['strength' => 1, 'damage' => 1],
            14 => ['strength' => 1, 'damage' => 1],
            15 => ['strength' => 2, 'damage' => 5],
        ],
    ];

    protected $listeners = [
        'characterUpdated' => 'updateCharacter', //оновлює на фронті кількість скілпоінтів
    ];

    public function mount($school_name = 'body')
    {
        $this->character = Auth::user()->character;
        $this->selectedSchool = $school_name;

        $availableSchools = ['body', 'strength'];

        $this->schools = School::where('character_id', $this->character->id)->get();

        foreach ($availableSchools as $school) {
            if (!$this->schools->where('school_name', $school)->first()) {

                $newSchool = School::create([
                    'character_id' => $this->character->id,
                    'school_name' => $school,
                    'level' => 0,
                ]);

                // Додаємо школу до колекції
                $this->schools->push($newSchool);
            }
        }

        $this->school = $this->schools->where('school_name', $this->selectedSchool)->first();

        $this->updateCharacter();
    }


    public function updateCharacter()
    {
        $this->character = auth()->user()->character;
        $this->skill_points = $this->character->skill_points;
    }

    public function learnLevel($level)
    {
        // Отримуємо активну школу за вибором користувача
        $this->school = $this->schools->where('school_name', $this->selectedSchool)->first();

        if (!$this->school) {
            return;
        }

        // Обчислюємо кількість витрачених скілпоінтів ДО оновлення рівня школи
        $pointsSpent = $level - $this->school->level;

        // Перевіряємо, чи вистачає скілпоінтів
        if ($pointsSpent > 0 && $pointsSpent <= $this->character->skill_points) {
            // Прокачуємо рівень школи
            $this->school->update(['level' => $level]);

            // Віднімаємо витрачені скілпоінти
            $this->character->decrement('skill_points', $pointsSpent);

            // Застосовуємо бонуси з масиву
            $this->applyBonusesFromLevels($level);

            // Отримуємо бонуси для конкретного рівня
            $schoolName = $this->school->school_name;
            $levelBonuses = $this->schoolLevels[$schoolName][$level];

            $currentSchoolLevelBodyBonus = $levelBonuses['body'] ?? 0;  // Тіло для цього рівня
            $currentSchoolLevelHealthBonus = isset($levelBonuses['max_health']) ? $levelBonuses['max_health'] : 0;
            //$currentSchoolLevelHealthBonus += $this->character->school_health_bonus ?? 0; //Кількість Тіла вивчається за кожен рівень школи

            //Кількість здоровʼя всі вивчені рівені
            $currentSchoolLevelHealthBonusOnly = $this->character->school_health_bonus + ($currentSchoolLevelBodyBonus * 10 + $currentSchoolLevelHealthBonus);
            $totalSchoolBodyBonus = $this->character->school_body_bonus + $currentSchoolLevelBodyBonus;

//            dump($currentSchoolLevelBodyBonus * 10 + $levelBonuses['max_health']);

            $currentSchoolLevelStrengthBonus = $levelBonuses['strength'] ?? 0;  // Сила для цього рівня
            $currentSchoolLevelDamageBonusModifier = $currentSchoolLevelStrengthBonus * 2;
            $currentSchoolLevelDamageBonus = isset($levelBonuses['damage']) ? $levelBonuses['damage'] : 0;
            $currentSchoolLevelDamageBonus += ($this->character->school_damage_bonus ?? 0) + $currentSchoolLevelDamageBonusModifier;
            $totalSchoolStrengthBonus = $this->character->school_strength_bonus + $currentSchoolLevelStrengthBonus;

            // Оновлюємо значення
            $totalHealth =  $currentSchoolLevelHealthBonusOnly + $this->character->base_health;
            $totalDamage = $this->character->base_damage + $currentSchoolLevelDamageBonus;

            // Оновлюємо інші параметри, якщо потрібно
            $this->character->update([
                'max_health' => $totalHealth,
//                'base_health' => $baseHealth,
                'school_health_bonus' => $currentSchoolLevelHealthBonusOnly,
                'school_body_bonus' => $totalSchoolBodyBonus,
                'school_damage_bonus' => $currentSchoolLevelDamageBonus,
                'school_strength_bonus' => $totalSchoolStrengthBonus,
                'damage' => $totalDamage,
            ]);

            // Оновлюємо дані в компоненті
            $this->dispatch('schoolUpdated',
                $totalHealth, $totalDamage
            );
        }
    }



    public function applyBonusesFromLevels($level)
    {
        // Отримуємо рівень школи і відповідні бонуси з масиву schoolLevels
        $schoolName = $this->school->school_name;
        $levelBonuses = $this->schoolLevels[$schoolName][$level] ?? null;

        if ($levelBonuses) {
            // Застосовуємо бонуси
            foreach ($levelBonuses as $attribute => $value) {
                $this->character->increment($attribute, $value);
            }
        }
    }


    public function closeModal()
    {
        $this->dispatch('closeSchool');
    }

    public function render()
    {
        return view('livewire.learn-school', [
            'schools' => $this->schools,
            'schoolLevels' => $this->schoolLevels,
            'selectedSchool' => $this->selectedSchool,
            'character' => $this->character
        ]);
    }


}
