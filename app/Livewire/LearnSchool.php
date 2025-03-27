<?php

namespace App\Livewire;

use App\Models\CharacterBuff;
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
    public $schools = [];
    public $selectedSchoolLabel;
    public $buffs = [];

    public $schoolLevels = [
        'body' => [
            1 => ['body' => 1, 'max_health' => 8],
            2 => ['body' => 1, 'max_health' => 8],
            3 => ['body' => 1, 'max_health' => 8],
            4 => ['body' => 1, 'max_health' => 8],
            5 => ['body' => 1, 'max_health' => 8],
            6 => ['body' => 1, 'max_health' => 8],
            7 => ['body' => 1, 'max_health' => 8],
            8 => ['body' => 1, 'max_health' => 8],
            9 => ['body' => 1, 'max_health' => 8],
            10 => ['body' => 1, 'max_health' => 8],
            11 => ['body' => 1, 'max_health' => 8],
            12 => ['body' => 1, 'max_health' => 8],
            13 => ['body' => 1, 'max_health' => 8],
            14 => ['body' => 1, 'max_health' => 8],
            15 => ['body' => 2, 'max_health' => 8],
        ],
        'strength' => [
            1 => ['strength' => 1, 'damage' => 1],
            2 => ['strength' => 1, 'damage' => 1],
            3 => ['strength' => 1, 'damage' => 1],
            4 => ['strength' => 1, 'max_health' => 4],
            5 => ['strength' => 1, 'damage' => 1],
            6 => ['strength' => 1, 'damage' => 1],
            7 => ['strength' => 1, 'damage' => 1],
            8 => ['strength' => 1, 'max_health' => 4],
            9 => ['strength' => 1, 'damage' => 1],
            10 => ['strength' => 1, 'damage' => 1],
            11 => ['strength' => 1, 'damage' => 1],
            12 => ['strength' => 1, 'max_health' => 4],
            13 => ['strength' => 1, 'damage' => 1],
            14 => ['strength' => 1, 'damage' => 1],
            15 => ['strength' => 2, 'damage' => 1],
        ],
        'dexterity' => [
            1 => ['dexterity' => 1, 'armor' => 3],
            2 => ['dexterity' => 1, 'damage' => 1],
            3 => ['dexterity' => 1, 'hit_chance' => 1],
            4 => ['dexterity' => 1, 'armor' => 3],
            5 => ['dexterity' => 1, 'damage' => 1],
            6 => ['dexterity' => 1, 'hit_chance' => 1],
            7 => ['dexterity' => 1, 'armor' => 3],
            8 => ['dexterity' => 1, 'damage' => 1],
            9 => ['dexterity' => 1, 'hit_chance' => 1],
            10 => ['dexterity' => 1, 'armor' => 3],
            11 => ['dexterity' => 1, 'damage' => 1],
            12 => ['dexterity' => 1, 'hit_chance' => 1],
            13 => ['dexterity' => 1, 'armor' => 3],
            14 => ['dexterity' => 1, 'damage' => 1],
            15 => ['dexterity' => 2, 'hit_chance' => 1],
        ],
        'intelligence' => [
            1 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            2 => ['intelligence' => 1, 'magic_damage' => 1],
            3 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            4 => ['intelligence' => 1, 'max_mana' => 3],
            5 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            6 => ['intelligence' => 1, 'magic_damage' => 1],
            7 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            8 => ['intelligence' => 1, 'max_mana' => 3],
            9 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            10 => ['intelligence' => 1, 'magic_damage' => 1],
            11 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            12 => ['intelligence' => 1, 'max_mana' => 3],
            13 => ['intelligence' => 1, 'magic_hit_chance' => 1],
            14 => ['intelligence' => 1, 'magic_damage' => 1],
            15 => ['intelligence' => 2, 'magic_hit_chance' => 1],
        ],
        'warlock' => [
            1 => ['body_boost' => 1, 'strength_boost' => 1],
            2 => ['body_boost' => 1, 'strength_boost' => 1],
            3 => ['dexterity_boost' => 1],
            4 => ['intelligence_boost' => 1],
            5 => ['intelligence_boost' => 1],
            6 => ['intelligence_boost' => 1],
            7 => ['intelligence_boost' => 1],
            8 => ['intelligence_boost' => 1],
            9 => ['body_boost' => 1],
            10 => ['body_boost' => 1],
            11 => ['body_boost' => 1],
            12 => ['body_boost' => 1],
            13 => ['body_boost' => 1],
            14 => ['body_boost' => 1],
            15 => ['body_boost' => 1],
        ],
    ];

    protected $listeners = [
        'characterUpdated' => 'updateCharacter', //оновлює на фронті кількість скілпоінтів
        'updateBuffsInCard' => 'loadBuffs',
        'buffActivated' => 'refreshCharacterAttributes',
    ];

    public function mount($school_name = 'body')
    {
        $this->character = Auth::user()->character;
        $this->selectedSchool = $school_name;

        $this->buffsActiveStatus = CharacterBuff::where('is_active', 1)
            ->pluck('is_active', 'id')
            ->toArray();

        $availableSchools = [
            'body' => 'Тіла',
            'strength' => 'Сили',
            'dexterity' => 'Спритності',
            'intelligence' => 'Інтелекту',
            'warlock' => 'Чаклунів',
        ];

        // Завантажуємо школи з бази
        $this->schools = School::where('character_id', $this->character->id)->get();

        // Перевіряємо наявність школи, якщо її немає — додаємо з лейблом
        foreach ($availableSchools as $school => $label) {
            if (!$this->schools->where('school_name', $school)->first()) {
                $newSchool = School::create([
                    'character_id' => $this->character->id,
                    'school_name' => $school,
                    'level' => 0,
                    'label' => $label,
                ]);

                // Додаємо нову школу до колекції
                $this->schools->push($newSchool);
            }
        }

        // Встановлюємо вибрану школу
        $this->school = $this->schools->where('school_name', $this->selectedSchool)->first();

        // Встановлюємо лейбл для обраної школи
        $this->selectedSchoolLabel = $availableSchools[$this->selectedSchool] ?? 'Unknown';

        // Оновлюємо бонуси та інші дані
        $this->updateCharacter();
    }

    public function loadBuffs()
    {
        $this->buffs = CharacterBuff::where('character_id', auth()->id())->get();
    }

    public function updatedSelectedSchool($school)
    {
        // Оновлюємо лейбл для нової вибраної школи
        $availableSchools = [
            'body' => 'Тіла',
            'strength' => 'Сили',
            'dexterity' => 'Спритності',
            'intelligence' => 'Інтелекту',
            'warlock' => 'Чаклунів',
        ];

        $this->selectedSchoolLabel = $availableSchools[$school] ?? 'Unknown';
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

            $currentSchoolLevelDexterityBonus = $levelBonuses['dexterity'] ?? 0;
            $totalSchoolDexterityBonus = $this->character->school_dexterity_bonus + $currentSchoolLevelDexterityBonus;

            $currentSchoolLevelDamageBonusModifier = $currentSchoolLevelStrengthBonus * 1;
            $currentSchoolLevelDexDamageModifier = $currentSchoolLevelDexterityBonus * 1;
            $currentSchoolLevelDamageBonus = isset($levelBonuses['damage']) ? $levelBonuses['damage'] : 0;
            $currentSchoolLevelDamageBonus += ($this->character->school_damage_bonus ?? 0) + $currentSchoolLevelDamageBonusModifier + $currentSchoolLevelDexDamageModifier;

            $currentSchoolLevelHitChanceBonusModifier = $currentSchoolLevelDexterityBonus * 2;
            $currentSchoolLevelHitChanceBonus = isset($levelBonuses['hit_chance']) ? $levelBonuses['hit_chance'] : 0;
            $currentSchoolLevelHitChanceBonus += ($this->character->school_hit_chance_bonus ?? 0) + $currentSchoolLevelHitChanceBonusModifier;

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

            $currentSchoolLevelMagicHitChanceBonusModifier = $currentSchoolLevelIntelligenceBonus * 2;
            $currentSchoolLevelMagicHitChanceBonus = isset($levelBonuses['magic_hit_chance']) ? $levelBonuses['magic_hit_chance'] : 0;
            $currentSchoolLevelMagicHitChanceBonus += ($this->character->school_magic_hit_chance_bonus ?? 0) + $currentSchoolLevelMagicHitChanceBonusModifier;

            $buffs = CharacterBuff::where('is_active', 1)->get();
            $buffAmount = 0;
            $buffIsActive = false;

            foreach ($buffs as $buff) {
                $buffAmount += $buff->buff_amount;
                $buffIsActive = true;
            }

            $bodySchoolBuffBonus = ($buffIsActive && isset($this->character->base_body))
                ? (int) round(($buffAmount / 100) * $this->character->base_body)
                : 0;
            $strengthSchoolBuffBonus = ($buffIsActive && isset($this->character->base_strength))
                ? (int) round(($buffAmount / 100) * $this->character->base_strength)
                : 0;
            $dexteritySchoolBuffBonus = ($buffIsActive && isset($this->character->base_dexterity))
                ? (int) round(($buffAmount / 100) * $this->character->base_dexterity)
                : 0;
            $intelligenceSchoolBuffBonus = ($buffIsActive && isset($this->character->base_intelligence))
                ? (int) round(($buffAmount / 100) * $this->character->base_intelligence)
                : 0;

            $healthSchoolBuffBonus = $bodySchoolBuffBonus > 0 ? $bodySchoolBuffBonus * 8 : 0;
            $manaSchoolBuffBonus = $intelligenceSchoolBuffBonus > 0 ? $intelligenceSchoolBuffBonus * 4 : 0;
            $damageSchoolBuffBonus = $strengthSchoolBuffBonus > 0 ? $strengthSchoolBuffBonus * 1 : 0;
            $magicDamageSchoolBuffBonus = $intelligenceSchoolBuffBonus > 0 ? $intelligenceSchoolBuffBonus * 1 : 0;
            $hitChanceSchoolBuffBonus = $dexteritySchoolBuffBonus > 0 ? $dexteritySchoolBuffBonus * 2 : 0;
            $magicHitChanceSchoolBuffBonus = $intelligenceSchoolBuffBonus > 0 ? $intelligenceSchoolBuffBonus * 2 : 0;
            $armorSchoolBuffBonus = $dexteritySchoolBuffBonus > 0 ? $dexteritySchoolBuffBonus * 3 : 0;

            // Оновлюємо значення
            $body = $this->character->base_body + $totalSchoolBodyBonus + $bodySchoolBuffBonus;
            $strength = $this->character->base_strength + $totalSchoolStrengthBonus + $strengthSchoolBuffBonus;
            $dexterity = $this->character->base_dexterity + $totalSchoolDexterityBonus + $dexteritySchoolBuffBonus;
            $intelligence = $this->character->base_intelligence + $totalSchoolIntelligenceBonus + $intelligenceSchoolBuffBonus;
            $totalHealth = $healthSchoolBuffBonus + $currentSchoolLevelHealthBonusOnly + $this->character->base_health;
            $totalMana = $manaSchoolBuffBonus + $currentSchoolLevelManaBonus + $this->character->base_mana;
            $totalDamage = $damageSchoolBuffBonus + $this->character->base_damage + $currentSchoolLevelDamageBonus;
            $totalMagicDamage = $magicDamageSchoolBuffBonus + $this->character->base_magic_damage + $currentSchoolLevelMagicDamageBonus;
            $totalArmor = $armorSchoolBuffBonus + $this->character->base_armor + $currentSchoolLevelArmorBonus;
            $totalHitChance = $hitChanceSchoolBuffBonus + $this->character->base_hit_chance + $currentSchoolLevelHitChanceBonus;
            $totalMagicHitChance = $magicHitChanceSchoolBuffBonus + $this->character->base_magic_hit_chance + $currentSchoolLevelMagicHitChanceBonus;

            // Оновлюємо інші параметри, якщо потрібно
            $this->character->update([
                'max_health' => $totalHealth,
                'school_health_bonus' => $currentSchoolLevelHealthBonusOnly,
                'damage' => $totalDamage,
                'school_damage_bonus' => $currentSchoolLevelDamageBonus,
                'hit_chance' => $totalHitChance,
                'school_hit_chance_bonus' => $currentSchoolLevelHitChanceBonus,
                'magic_damage' => $totalMagicDamage,
                'school_magic_damage_bonus' => $currentSchoolLevelMagicDamageBonus,
                'armor' => $totalArmor,
                'max_mana' => $totalMana,
                'school_mana_bonus' => $currentSchoolLevelManaBonus,
                'magic_hit_chance' => $totalMagicHitChance,
                'school_magic_hit_chance_bonus' => $currentSchoolLevelMagicHitChanceBonus,
                'body' => $body,
                'school_body_bonus' => $totalSchoolBodyBonus,
                'strength' => $strength,
                'school_strength_bonus' => $totalSchoolStrengthBonus,
                '$dexterity' => $dexterity,
                'school_dexterity_bonus' => $totalSchoolDexterityBonus,
                'intelligence' => $intelligence,
                'school_intelligence_bonus' => $totalSchoolIntelligenceBonus,
                'school_armor_bonus' => $currentSchoolLevelArmorBonus,
            ]);

            // Оновлюємо дані в компоненті
            $this->dispatch('schoolUpdated',
                $totalHealth, $totalDamage, $totalMagicDamage, $totalArmor, $totalMana, $totalHitChance, $totalMagicHitChance
            );
        }
    }

    public function applyBonusesFromLevels($level)
    {
        $schoolName = $this->school->school_name;

        $AttrBuffLevels = [
            1 => 128,
            2 => 32,
            3 => 64,
            4 => 32,
            5 => 32,
            6 => 32,
            7 => 32,
            8 => 32,
            9 => 32,
            10 => 32,
        ];

        if ($schoolName === 'warlock') {
            $levelBonuses = $this->schoolLevels[$schoolName][$level] ?? null;

            if ($levelBonuses) {
                foreach ($levelBonuses as $buffName => $buffAmount) {
                    $existingBuff = $this->character->buffs()->where('buff_name', $buffName)->first();

                    if ($existingBuff) {
                        $newBuffAmount = $existingBuff->buff_amount + $AttrBuffLevels[$level];
                        $existingBuff->update([
                            'buff_amount' => $newBuffAmount,
                            'level' => $level,
                        ]);
                    } else {
                        $this->character->buffs()->create([
                            'buff_name' => $buffName,
                            'buff_amount' => $AttrBuffLevels[$level],
                            'level' => $level,
                            'label' => $this->getBuffLabel($buffName),
                        ]);
                    }
                }
            }
        } else {
            $levelBonuses = $this->schoolLevels[$schoolName][$level] ?? null;

            if ($levelBonuses) {
                foreach ($levelBonuses as $attribute => $value) {
                    $this->character->increment($attribute, $value);
                }
            }
        }

        $this->dispatch('buffAdded');
    }


    private function getBuffLabel($buffName)
    {
        $labels = [
            'body_boost' => 'Ведмежа кров',
            'strength_boost' => 'Сила тигра',
            'dexterity_boost' => 'Котяча грація',
            'intelligence_boost' => 'Мудрість сови',
        ];

        return $labels[$buffName] ?? ucfirst(str_replace('_', ' ', $buffName));
    }

    public function refreshCharacterAttributes()
    {
        $this->character->refresh();
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
