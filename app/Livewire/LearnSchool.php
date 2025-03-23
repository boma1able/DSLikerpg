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
        'dexterity' => [
            1 => ['dexterity' => 1, 'armor' => 3],
            2 => ['dexterity' => 1, 'armor' => 3],
            3 => ['dexterity' => 1, 'armor' => 3],
            4 => ['dexterity' => 1, 'armor' => 3],
            5 => ['dexterity' => 1, 'armor' => 3],
            6 => ['dexterity' => 1, 'armor' => 3],
            7 => ['dexterity' => 1, 'armor' => 3],
            8 => ['dexterity' => 1, 'armor' => 3],
            9 => ['dexterity' => 1, 'armor' => 3],
            10 => ['dexterity' => 1, 'armor' => 3],
            11 => ['dexterity' => 1, 'armor' => 3],
            12 => ['dexterity' => 1, 'armor' => 3],
            13 => ['dexterity' => 1, 'armor' => 3],
            14 => ['dexterity' => 1, 'armor' => 3],
            15 => ['dexterity' => 1, 'armor' => 3],
        ],
        'intelligence' => [
            1 => ['intelligence' => 1, 'magic_damage' => 1],
            2 => ['intelligence' => 1, 'magic_damage' => 1],
            3 => ['intelligence' => 1, 'magic_damage' => 1],
            4 => ['intelligence' => 1, 'magic_damage' => 1],
            5 => ['intelligence' => 1, 'magic_damage' => 1],
            6 => ['intelligence' => 1, 'magic_damage' => 1],
            7 => ['intelligence' => 1, 'magic_damage' => 1],
            8 => ['intelligence' => 1, 'magic_damage' => 1],
            9 => ['intelligence' => 1, 'magic_damage' => 1],
            10 => ['intelligence' => 1, 'magic_damage' => 1],
            11 => ['intelligence' => 1, 'magic_damage' => 1],
            12 => ['intelligence' => 1, 'magic_damage' => 1],
            13 => ['intelligence' => 1, 'magic_damage' => 1],
            14 => ['intelligence' => 1, 'magic_damage' => 1],
            15 => ['intelligence' => 1, 'magic_damage' => 1],
        ],
    ];

    protected $listeners = [
        'characterUpdated' => 'updateCharacter', //оновлює на фронті кількість скілпоінтів
    ];

    public function mount($school_name = 'body')
    {
        $this->character = Auth::user()->character;
        $this->selectedSchool = $school_name;

        $availableSchools = ['body', 'strength', 'dexterity', 'intelligence'];

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
            $totalSchoolBodyBonus = $this->character->school_body_bonus + $currentSchoolLevelBodyBonus;

            $currentSchoolLevelHealthBonus = isset($levelBonuses['max_health']) ? $levelBonuses['max_health'] : 0;
            $currentSchoolLevelHealthBonusOnly = $this->character->school_health_bonus + ($currentSchoolLevelBodyBonus * 8 + $currentSchoolLevelHealthBonus);

            $currentSchoolLevelStrengthBonus = $levelBonuses['strength'] ?? 0;
            $totalSchoolStrengthBonus = $this->character->school_strength_bonus + $currentSchoolLevelStrengthBonus;

            $currentSchoolLevelDamageBonusModifier = $currentSchoolLevelStrengthBonus * 1;
            $currentSchoolLevelDamageBonus = isset($levelBonuses['damage']) ? $levelBonuses['damage'] : 0;
            $currentSchoolLevelDamageBonus += ($this->character->school_damage_bonus ?? 0) + $currentSchoolLevelDamageBonusModifier;

            $currentSchoolLevelDexterityBonus = $levelBonuses['dexterity'] ?? 0;
            $totalSchoolDexterityBonus = $this->character->school_dexterity_bonus + $currentSchoolLevelDexterityBonus;

            $currentSchoolLevelArmorBonusModifier = $currentSchoolLevelDexterityBonus * 3;
            $currentSchoolLevelArmorBonus = isset($levelBonuses['armor']) ? $levelBonuses['armor'] : 0;
            $currentSchoolLevelArmorBonus += ($this->character->school_armor_bonus ?? 0) + $currentSchoolLevelArmorBonusModifier;

            $currentSchoolLevelIntelligenceBonus = $levelBonuses['intelligence'] ?? 0;
            $totalSchoolIntelligenceBonus = $this->character->school_intelligence_bonus + $currentSchoolLevelIntelligenceBonus;

            $currentSchoolLevelManaBonusModifier = $currentSchoolLevelIntelligenceBonus * 4;
            $currentSchoolLevelManaBonus = isset($levelBonuses['max_mana']) ? $levelBonuses['max_mana'] : 0;
            $currentSchoolLevelManaBonus += ($this->character->school_mana_bonus ?? 0) + $currentSchoolLevelManaBonusModifier;

            $currentSchoolLevelMagicDamageBonusModifier = $currentSchoolLevelIntelligenceBonus * 1;
            $currentSchoolLevelMagicDamageBonus = isset($levelBonuses['magic_damage']) ? $levelBonuses['magic_damage'] : 0;
            $currentSchoolLevelMagicDamageBonus += ($this->character->school_magic_damage_bonus ?? 0) + $currentSchoolLevelMagicDamageBonusModifier;

//            dd($currentSchoolLevelArmorBonus);

            // Оновлюємо значення
            $totalHealth =  $currentSchoolLevelHealthBonusOnly + $this->character->base_health;
            $totalDamage = $this->character->base_damage + $currentSchoolLevelDamageBonus;
            $totalMagicDamage = $this->character->base_magic_damage + $currentSchoolLevelMagicDamageBonus;
            $totalArmor = $this->character->base_armor + $currentSchoolLevelArmorBonus;
            $totalMana = $this->character->base_mana + $currentSchoolLevelManaBonus;

            // Оновлюємо інші параметри, якщо потрібно
            $this->character->update([
                'max_health' => $totalHealth,
                'damage' => $totalDamage,
                'magic_damage' => $totalMagicDamage,
                'armor' => $totalArmor,
                'max_mana' => $totalMana,
                'school_health_bonus' => $currentSchoolLevelHealthBonusOnly,
                'school_mana_bonus' => $currentSchoolLevelManaBonus,
                'school_damage_bonus' => $currentSchoolLevelDamageBonus,
                'school_magic_damage_bonus' => $currentSchoolLevelMagicDamageBonus,
                'school_body_bonus' => $totalSchoolBodyBonus,
                'school_strength_bonus' => $totalSchoolStrengthBonus,
                'school_dexterity_bonus' => $totalSchoolDexterityBonus,
                'school_intelligence_bonus' => $totalSchoolIntelligenceBonus,
                'school_armor_bonus' => $currentSchoolLevelArmorBonus,
            ]);

            // Оновлюємо дані в компоненті
            $this->dispatch('schoolUpdated',
                $totalHealth, $totalDamage, $totalMagicDamage, $totalArmor, $totalMana
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
