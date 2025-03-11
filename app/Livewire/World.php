<?php

namespace App\Livewire;

use App\Models\Character;
use App\Models\Monster;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Services\MonsterEncounterService;

class World extends Component
{
    public array $map;
    public $loggedIn = false;

    public int $offsetX;
    public int $offsetY;
    public int $characterX;
    public int $characterY;

    public array $character = [];
    public $userName;
    public $experience;
    public $level;
    public $requiredExperience;
    public $gold;

    public ?Monster $monster = null;
    public $currentMonster = null;
    public $currentTargetMonsterId;
    public array $monsters = [];
    public $showMonsterHealth = false;
    public bool $monsterAttacked = false;
    public bool $inBattle = false;
    public array $log = [];
    public string $message = '';

    public bool $isResting = false;
    public bool $restStarted = false;
    public bool $hasRestingInterruptedMessage = false;
    public bool $isMoving = false;
//    public int $lastMoveTime = 0;
    public bool $showReviveModal = false;

    public bool $welcomeMessageShown = false;

    protected $listeners = [
        'updateCharacterStats' => 'updateStats',
        'updateMap' => 'handleUpdateMap',
        'resting' => 'startResting',
        'respawnMonster' => 'dispatchRespawnMonster',
        'stopResting' => 'interruptResting',
        'logUpdated' => 'handleLogUpdated'
    ];

    public function mount()
    {
        if (Auth::check()) {
            $this->loggedIn = true;

            $this->userName = auth()->user()->name;

            if (auth()->user()->character) {
                $character = auth()->user()->character;
            } else {
                return $this->redirectRoute('register');
            }
        } else {
            return $this->redirectRoute('login');
        }

        $this->characterX = $character->spawn_x;
        $this->characterY = $character->spawn_y;
        $this->dispatch('updateCharacterPosition', $this->characterX, $this->characterY);

        $this->map = json_decode(file_get_contents(storage_path('app/map.json')), true);

        $message = "👋 Вітаю " . $character->user->name . ", ласкаво просимо до гри!";
        $this->addLogMessage($message);

        $this->characterX = floor(count($this->map[0]) / 2);
        $this->characterY = floor(count($this->map) / 2);

        // Встановлюємо зсув, щоб мапа була в центрі
        $this->offsetX = -($this->characterX * 1) + 0;
        $this->offsetY = -($this->characterY * 1) + 0;

        $this->character = [
            'id' => $character->id,
            'health' => $character->health,
            'max_health' => $character->max_health,
            'experience' => $character->experience,
            'mana' => $character->mana,
            'max_mana' => $character->max_mana,
            'level' => $character->level,
            'race' => $character->race,
            'avatar' => $character->avatar,
            'class' => $character->class,
            'gold' => $character->gold,
            'damage' => $character->damage,
            'position_x' => $this->characterX,
            'position_y' => $this->characterY,
            'body' => $character->body,
            'strength' => $character->strength,
            'agility' => $character->agility,
            'intelligence' => $character->intelligence,
        ];

        $character = Character::find($this->character['id']);
        if ($character) {
            $this->experience = $character->experience;
            $this->level = $character->level;
            $this->requiredExperience = $this->getRequiredExperienceForLevel($this->level + 1);
        }

        $this->experience = $character ? $character->experience : 0;

        $this->spawnMonsters();
    }

    public function handleUpdateMap($map, $offsetX, $offsetY)
    {
        $this->map = $map;
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
    }


    public function addLogMessage($message)
    {
        $currentTime = now()->format('H:i:s');
        $this->log[] = "<span class='inline-block w-[45px] text-[#a0a0a0] mr-2 text-xs'>{$currentTime}</span>{$message}";
        $this->dispatch('addLogMessage', $message);
    }

    public function handleLogUpdated($log)
    {
        $this->log = $log;
    }

    public function startResting($key)
    {
        if ($this->inBattle) {
            $this->addLogMessage("Бій триває! Медитація неможлива.");
            return;
        }

        if ($key === 'z') {
            $this->isResting = true;
            $this->hasRestingInterruptedMessage = false;
        }
    }

    public function restoreHealth()
    {
        if (!$this->isResting || $this->inBattle) {
            return;
        }

        $healingAmount = 50;

        if ($this->character['health'] < $this->character['max_health']) {
            $this->character['health'] = min($this->character['health'] + $healingAmount, $this->character['max_health']);

            if (!$this->restStarted) {
                $this->addLogMessage('Ви почали медитацію!');
                $this->restStarted = true;
            }
        } else {
            $this->isResting = false;
            $this->restStarted = false;
            if (!$this->hasRestingInterruptedMessage) {
                $this->addLogMessage('✅ Відновлення завершено!');
            }
        }

        $this->updateCharacterInDatabase();

        $this->dispatch('updateHealth', $this->character['health']);
    }

    public function interruptResting()
    {
        if ($this->isResting) {
            $this->isResting = false;
            $this->restStarted = false;
            $this->addLogMessage('⛔ Відновлення перервано через рух!');

            $this->dispatch('stopResting', ['isResting' => false]);
        }
    }

    public function addMonstersToMap($monsters, $isRespawn = false)
    {
        // Якщо немає монстрів, виходимо
        if (empty($monsters)) {
            return;
        }

        // Шукаємо всі клітинки, де можна рухатись
        $validPositions = [];

        foreach ($this->map as $y => $row) {
            foreach ($row as $x => $tile) {
                if ($tile !== "x") {
                    $validPositions[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        // Якщо немає місць, виходимо
        if (empty($validPositions)) {
            return;
        }

        // Додаємо монстрів на мапу
        foreach ($monsters as $monster) {
            // Випадково вибираємо позицію для нових монстрів, якщо це не респаун
            if (!$isRespawn) {
                $randomKey = array_rand($validPositions);
                $randomPosition = $validPositions[$randomKey];
            } else {
                // Якщо респаун, використовуємо позицію, яка була передана через подію
                $randomPosition = ['x' => $monster['position_x'], 'y' => $monster['position_y']];
            }

            // Додаємо монстра на мапу
            $this->monsters[] = [
                'id' => $monster['id'],
                'name' => $monster['name'],
                'level' => $monster['level'],
                'experience' => $monster['experience'],
                'avatar' => $monster['avatar'],
                'max_health' => $monster['health'],
                'health' => $monster['health'],
                'damage' => $monster['damage'],
                'hit_chance' => $monster['hit_chance'],
                'position_x' => $randomPosition['x'],
                'position_y' => $randomPosition['y'],
                'gold_min' => $monster['gold_min'],
                'gold_max' => $monster['gold_max'],
            ];
        }
    }

    public function spawnMonsters($count = 10, $specificMonsterId = null)
    {
        // Якщо передано конкретного монстра, знаходимо його
        if ($specificMonsterId) {
            $monsters = Monster::where('id', $specificMonsterId)->get()->toArray();
        } else {
            // Отримуємо випадкових монстрів з бази
            $monsters = Monster::inRandomOrder()->limit($count)->get()->toArray();
        }

        // Викликаємо спільну функцію для додавання монстрів на мапу
        $this->addMonstersToMap($monsters);
    }

    public function dispatchRespawnMonster($monsterData)
    {
        // Витягуємо монстра з бази даних за його ID
        $monster = Monster::find($monsterData['id']);

        if ($monster) {
            // Отримуємо випадкову позицію
            $validPositions = $this->getValidPositions(); // Отримуємо допустимі позиції з карти
            $randomKey = array_rand($validPositions);
            $randomPosition = $validPositions[$randomKey];

            // Викликаємо спільну функцію для респауну монстра з новою випадковою позицією
            $this->addMonstersToMap([[
                'id' => $monster->id,
                'name' => $monster->name,
                'level' => $monster->level,
                'experience' => $monster->experience,
                'avatar' => $monster->avatar,
                'health' => $monster->health,
                'damage' => $monster->damage,
                'hit_chance' => $monster->hit_chance,
                'position_x' => $randomPosition['x'],
                'position_y' => $randomPosition['y'],
                'gold_min' => $monster['gold_min'],
                'gold_max' => $monster['gold_max'],
            ]], true);
        }
    }

    public function getValidPositions()
    {
        $validPositions = [];

        foreach ($this->map as $y => $row) {
            foreach ($row as $x => $tile) {
                if ($tile !== "x") {
                    $validPositions[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        return $validPositions;
    }

    // Функція для старту бою
    public function startBattle($monsterId)
    {
        if ($this->inBattle) {
            return;
        }

        $monster = collect($this->monsters)->firstWhere('id', $monsterId);

        if (!$monster) {
            $this->addLogMessage("❌ Монстр не знайдений!");
            return;
        }

        // Запам’ятовуємо цього монстра як ціль
        $this->currentTargetMonsterId = $monsterId;

        // Перериваємо відпочинок, якщо персонаж відпочиває
        if ($this->isResting) {
            $this->isResting = false;
            $this->restStarted = false;

            if (!$this->hasRestingInterruptedMessage) {
                $this->addLogMessage("⚔ Бій починається, відпочинок припинено.");
                $this->hasRestingInterruptedMessage = true;
            }

            // Надсилаємо подію про припинення відпочинку
            $this->dispatch('stopResting');
        }

        // Встановлюємо стан бою
        $this->inBattle = true;
        $this->addLogMessage("🔥 Ви вступили в бій з {$monster['name']}!");

        // Починаємо бій саме з обраним монстром
        $this->fight($monsterId);
    }


    // Функція для бою
    public function fight($monsterId)
    {
        if (!$this->inBattle || $this->currentTargetMonsterId !== $monsterId) {
            return;
        }

        // Шукаємо монстра за ID
        $monsterIndex = collect($this->monsters)->search(fn($m) => $m['id'] == $monsterId);

        if ($monsterIndex === false) {
            return;
        }

        // Отримуємо монстра за посиланням, щоб змінювати масив напряму
        $monster = &$this->monsters[$monsterIndex];

        // Перевірка на шанс попадання персонажа
        $characterHitChance = $this->calculateCharacterHitChance($this->character, $monster);
        if (rand(0, 100) / 100 <= $characterHitChance) {
            // Якщо персонаж потрапив
            $monster['health'] -= $this->character['damage'];
            $this->addLogMessage("⚔️ Ви вдарили {$monster['name']} на {$this->character['damage']} HP.");
        } else {
            // Якщо персонаж не потрапив
            $this->addLogMessage("❌ Ви промахнулися по {$monster['name']}.");
        }

        // Оновлюємо HP персонажа в масиві після отриманого пошкодження
        $this->character['health'] -= $monster['damage'];

        // Оновлюємо базу і відправляємо відразу після зміни
        $this->updateCharacterInDatabase();

        // Емітуємо подію для оновлення хп у Blade компоненті
        $this->dispatch('characterUpdated', $this->character['health']);

        // Якщо монстр помер
        if ($monster['health'] <= 0) {
            $this->addLogMessage("Ви перемогли {$monster['name']}!");

            $xpGained = $this->calculateExperienceGain($this->character, $monster);
            $this->experience += $xpGained;
            $this->addLogMessage("Ви отримали {$xpGained} exp!");

            // Оновлюємо досвід і рівень персонажа
            $this->updateCharacterLevel();

            $goldAmount = rand($monster['gold_min'], $monster['gold_max']);
            $this->character['gold'] += $goldAmount;
            $character = Character::find($this->character['id']);
            $character->gold = $this->character['gold'];
            $character->save();
            $this->dispatch('goldUpdated', $this->character['gold']);
            $this->addLogMessage("Ви отримали $goldAmount золота від {$monster['name']}!");

            $respawnMonster = $this->monsters[$monsterIndex];

            unset($this->monsters[$monsterIndex]);
            $this->monsters = array_values($this->monsters);

            // Скидаємо ціль після перемоги
            $this->currentTargetMonsterId = null;
            $this->inBattle = false;

            // Викликаємо dispatch для респауну монстра
            $this->dispatch('respawn-monster-js', [$respawnMonster]);

            return;
        }

        // Монстр атакує гравця
        $monsterHitChance = $this->calculateHitChance($monster, $this->character);
        if (rand(0, 100) / 100 <= $monsterHitChance) {
            // Якщо монстр потрапив
            $this->character['health'] -= $monster['damage'];
            $this->addLogMessage("💀 {$monster['name']} вдарив вас на {$monster['damage']} HP.");
        } else {
            // Якщо монстр не потрапив
            $this->addLogMessage("❌ {$monster['name']} промахнувся.");
        }

        // Оновлюємо HP персонажа в масиві після отриманого пошкодження
        $this->updateCharacterInDatabase(); // Оновлюємо базу після кожного пошкодження

        // Якщо гравець мертвий
        if ($this->character['health'] <= 0) {
            $this->addLogMessage('💀 Ви загинули! Гра закінчена!');
            $this->inBattle = false;
            $this->dispatch('showReviveButton');
            $this->showReviveModal = true;
        }

        $this->updateCharacterInDatabase();
    }

    public function levelUp()
    {
        // Логіка підвищення рівня
        $this->character['level']++;

        // Оновлення атрибутів
        $this->updateCharacterAttributes();

        // Відправка події, що рівень підвищено
        $this->dispatch('levelUp');
    }


    protected function updateCharacterInDatabase()
    {
        $character = Character::find($this->character['id']);
        if ($character) {
            $character->health = $this->character['health']; // Оновлюємо здоров'я
            // Перевірка, щоб здоров'я не стало менше 0
            if ($this->character['health'] < 0) {
                $this->character['health'] = 0;
            }
            $character->experience = $this->experience; // Оновлюємо досвід
            $character->save(); // Зберігаємо зміни в базі
        }
    }


    public function calculateExperienceGain($character, $monster): int
    {
        $baseExp = $monster['experience'];

        // Різниця рівнів між персонажем і монстром
        $levelDifference = $monster['level'] - $character['level'];

        // Бонус або штраф за рівень моба
        if ($levelDifference > 0) {
            $modifier = 1 + ($levelDifference * 0.1); // +10% за кожен рівень вище
        } elseif ($levelDifference < 0) {
            $modifier = max(0, 1 + ($levelDifference * 0.2)); // -10% за кожен рівень нижче, мінімум 0
        } else {
            $modifier = 1; // Без змін
        }

        $expGain = (int) round($baseExp * $modifier);

        // Зберігаємо досвід в базу
        $characterId = $character['id'];
        $character = Character::find($characterId);
        if ($character) {
            $character->experience += $expGain;
            $character->save();

            // Перевіряємо, чи потрібно підвищити рівень
            $this->checkAndLevelUp($character);

            // Оновлюємо дані в реальному часі в компоненті CharacterCard
            $this->dispatch('characterUpdated');
        }

        return $expGain;
    }


// Обчислення досвіду для наступного рівня
    public function getRequiredExperienceForLevel($level): int
    {
        return (int) round(100 + ($level - 1) * 50 + pow(1.1, $level) * 20);
    }

// Оновлюємо рівень персонажа, якщо необхідно
    public function checkAndLevelUp(Character $character)
    {
        $requiredExperience = $this->getRequiredExperienceForLevel($character->level + 1);

        while ($character->experience >= $requiredExperience) {
            $character->experience -= $requiredExperience;
            $character->level++;

            // Оновлюємо параметри...

            $character->save();

            // Логування події
            $this->addLogMessage("<span class='text-green-600'>Вітаю! Ви досягли рівня {$character->level}!</span>");

            // Перевіряємо ще раз (якщо вистачає досвіду на кілька рівнів)
            $requiredExperience = $this->getRequiredExperienceForLevel($character->level + 1);
        }

        // Відправляємо подію для оновлення даних
        $this->dispatch('characterUpdated');
    }

// Оновлюємо досвід для відображення
    public function updateCharacterLevel()
    {
        $character = Character::find($this->character['id']);
        if ($character) {
            $this->experience = $character->experience;
            $this->level = $character->level;
            $this->requiredExperience = $this->getRequiredExperienceForLevel($this->level + 1);
        }
    }

    public function updateCharacterAfterHit(Character $character, int $damageReceived, int $manaUsed, int $expGain)
    {
        // Оновлення хп і мп
        $character->health -= $damageReceived;
        $character->mana -= $manaUsed;

        // Переконатися, що хп та мп не стають менше 0
        $character->health = max(0, $character->health);
        $character->mana = max(0, $character->mana);

        // Оновлюємо досвід
        $character->experience += $expGain;

        // Перевіряємо рівень
        $this->checkAndLevelUp($character);

        // Зберігаємо зміни в базу даних
        $character->save();

        // Повертаємо оновлені значення
        return [
            'health' => $character->health,
            'mana' => $character->mana,
            'experience' => $character->experience,
            'level' => $character->level,
        ];
    }


    public function calculateHitChance($attacker, $defender)
    {
        // Різниця в рівнях
        $levelDifference = $attacker['level'] - $defender['level'];

        // Початковий шанс попадання
        $hitChance = $attacker['hit_chance'];

        // Збільшення або зменшення шансів залежно від рівня
        if ($levelDifference > 0) {
            $hitChance += 0.05 * $levelDifference; // Збільшуємо на 5% за кожен рівень
        } elseif ($levelDifference < 0) {
            $hitChance -= 0.05 * abs($levelDifference); // Зменшуємо на 5% за кожен рівень
        }

        // Обмеження шансів від 0 до 1
        return max(0, min(1, $hitChance));
    }

    public function calculateCharacterHitChance($character, $monster)
    {
        // Різниця в рівнях
        $levelDifference = $character['level'] - $monster['level'];

        // Початковий шанс попадання (наприклад, 75% для всіх персонажів)
        $hitChance = 0.75;

        // Збільшення або зменшення шансів залежно від рівня
        if ($levelDifference > 0) {
            $hitChance += 0.05 * $levelDifference; // Збільшуємо на 5% за кожен рівень
        } elseif ($levelDifference < 0) {
            $hitChance -= 0.05 * abs($levelDifference); // Зменшуємо на 5% за кожен рівень
        }

        // Обмеження шансів від 0 до 1
        return max(0, min(1, $hitChance));
    }

    public function removeMonsterBorder()
    {
        $this->monsterAttacked = false;
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.world', [
            'map' => $this->map,
            'characterX' => $this->characterX,
            'characterY' => $this->characterY,
            'monsters' => $this->monsters,
            'character' => $this->character,
            'userName' => $this->userName,

        ]);
    }
}
