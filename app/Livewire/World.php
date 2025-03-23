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
    public $characterPositionX;
    public $characterPositionY;
    public $reviveCoordinates = ['x' => 5, 'y' => 4];

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
    public bool $showReviveModal = false;

    public bool $welcomeMessageShown = false;
    public array $objects = [];
    public $showStats = false;
    public $showSchool = false;

    protected $listeners = [
        'closeStats',
        'closeSchool',
        'characterMoved' => 'updateCharacterPositionForMonster',
        'updateMap' => 'handleUpdateMap',
        'resting' => 'startResting',
        'respawnMonster' => 'dispatchRespawnMonster',
        'stopResting' => 'interruptResting',
        'logUpdated' => 'handleLogUpdated'
    ];

    public function updateCharacterPositionForMonster($characterX, $characterY)
    {
        // Оновлення координат персонажа
        $this->characterPositionX = $characterX;
        $this->characterPositionY = $characterY;
    }

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

        $this->map = json_decode(file_get_contents(storage_path('app/map.json')), true);

        $message = "👋 Вітаю " . $character->user->name . ", ласкаво просимо до гри!";
        $this->addLogMessage($message);

        // Ініціалізація координат
        $this->characterX = $character->position_x;
        $this->characterY = $character->position_y;

        // Встановлення офсету з даних з бази
        $this->offsetX = $character->offset_x;
        $this->offsetY = $character->offset_y;

        // Якщо офсети ще не задані, використовуємо значення по замовчуванню
        if ($this->offsetX === null || $this->offsetY === null) {
            $this->offsetX = -$this->characterX;
            $this->offsetY = -$this->characterY;
        }

        $this->character = [
            'id' => $character->id,
            'health' => $character->health,
            'max_health' => $character->max_health,
            'experience' => $character->experience,
            'skill_points' => $character->skill_points,
            'mana' => $character->mana,
            'max_mana' => $character->max_mana,
            'level' => $character->level,
            'race' => $character->race,
            'avatar' => $character->avatar,
            'class' => $character->class,
            'gold' => $character->gold,
            'damage' => $character->damage,
            'armor' => $character->armor,
            'position_x' => $this->characterX,
            'position_y' => $this->characterY,
            'body' => $character->body,
            'strength' => $character->strength,
            'dexterity' => $character->dexterity,
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

        $this->objects[] = [
            'name' => 'Зірка',
            'position_x' => 5,
            'position_y' => 4,
            'type' => 'star',
        ];

        $this->objects[] = [
            'name' => 'Школа',
            'position_x' => 4,
            'position_y' => 3,
            'type' => 'skills',
        ];


        $this->dispatch('updateCharacterPosition', $this->characterX, $this->characterY);

        $this->dispatch('refresh-inventory');
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
        if (empty($monsters)) {
            return;
        }

        $validPositions = $this->getValidPositions();

        if (empty($validPositions)) {
            return;
        }

        foreach ($monsters as $monster) {
            do {
                if (!$isRespawn) {
                    $randomKey = array_rand($validPositions);
                    $randomPosition = $validPositions[$randomKey];
                } else {
                    $randomPosition = ['x' => $monster['position_x'], 'y' => $monster['position_y']];
                }

                // Перевірка, чи є в цій точці об'єкт
                $objectAtPosition = collect($this->objects)->first(fn($obj) => $obj['position_x'] === $randomPosition['x'] && $obj['position_y'] === $randomPosition['y']);
            } while ($objectAtPosition); // Повторюємо, поки точка зайнята

            // Додаємо монстра до карти, без унікального ID
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

            // Оновлюємо позицію монстра в базі даних
            Monster::where('id', $monster['id'])->update([
                'position_x' => $randomPosition['x'],
                'position_y' => $randomPosition['y'],
            ]);
        }
    }

    public function spawnMonsters($specificMonsterId = null)
    {
        $monsters = $specificMonsterId
            ? Monster::where('id', $specificMonsterId)->get()->toArray()
            : Monster::all()->toArray();

        $this->addMonstersToMap($monsters);
    }

    public function dispatchRespawnMonster($monsterData)
    {
        $monster = Monster::find($monsterData['id']);

        if ($monster) {
            $validPositions = $this->getValidPositions();
            $randomKey = array_rand($validPositions);
            $randomPosition = $validPositions[$randomKey];

            $monster->update([
                'position_x' => $randomPosition['x'],
                'position_y' => $randomPosition['y'],
            ]);

            // Додаємо респавненого монстра на карту
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
                'gold_min' => $monster->gold_min,
                'gold_max' => $monster->gold_max,
            ]], true);
        }
    }

    public function moveMonsters()
    {
        foreach ($this->monsters as &$monster) {
            if ($this->inBattle) {
                return;
            }

            $possibleMoves = [
                ['x' => 0, 'y' => -1],
                ['x' => 0, 'y' => 1],
                ['x' => -1, 'y' => 0],
                ['x' => 1, 'y' => 0]
            ];

            shuffle($possibleMoves);

            $initialMonsterX = $monster['position_x'];
            $initialMonsterY = $monster['position_y'];

            foreach ($possibleMoves as $move) {
                $newMonsterX = $monster['position_x'] + $move['x'];
                $newMonsterY = $monster['position_y'] + $move['y'];

                if (isset($this->map[$newMonsterY][$newMonsterX]) && $this->map[$newMonsterY][$newMonsterX] !== "x") {
                    $objectAtNewPosition = collect($this->objects)->first(fn($obj) => $obj['position_x'] === $newMonsterX && $obj['position_y'] === $newMonsterY);

                    if ($objectAtNewPosition) {
                        continue; // Пропускаємо хід, якщо є об'єкт
                    }

                    if ($initialMonsterX === $this->characterPositionX && $initialMonsterY === $this->characterPositionY) {
                        $direction = $move['x'] == -1 ? 'на захід' :
                            ($move['x'] == 1 ? 'на схід' :
                                ($move['y'] == -1 ? 'на північ' : 'на південь'));

                        $this->addLogMessage("<span class='text-gray-400'>{$monster['name']} пішов $direction.</span>");
                    }

                    Monster::where('id', $monster['id'])->update([
                        'position_x' => $newMonsterX,
                        'position_y' => $newMonsterY,
                    ]);

                    $monster['position_x'] = $newMonsterX;
                    $monster['position_y'] = $newMonsterY;

                    if ($newMonsterX === $this->characterPositionX && $newMonsterY === $this->characterPositionY) {
                        $message = MonsterEncounterService::getMessage($monster['name']);
                        $this->addLogMessage("<span class='text-gray-400'>$message</span>");
                    }

                    $this->dispatch('monstersUpdated', $this->monsters);
                    break;
                }
            }
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
                $this->addLogMessage("Бій починається, відпочинок припинено.");
                $this->hasRestingInterruptedMessage = true;
            }

            // Надсилаємо подію про припинення відпочинку
            $this->dispatch('stopResting');
        }

        // Встановлюємо стан бою
        $this->inBattle = true;
        $this->addLogMessage("<span class='text-red-600'>Ви вступили в бій з {$monster['name']}!</span>");

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
            $this->addLogMessage("<span class='text-blue-600'>Ви вдарили {$monster['name']} на {$this->character['damage']} HP.</span>");
        } else {
            // Якщо персонаж не потрапив
            $this->addLogMessage("<span class='text-blue-600'>Ви промахнулися по {$monster['name']}.</span>");
        }

        // Якщо монстр помер
        if ($monster['health'] <= 0) {
            $this->addLogMessage("Ви перемогли {$monster['name']}!");

            $xpGained = $this->calculateExperienceGain($this->character, $monster);
            $this->experience += $xpGained;
            $this->addLogMessage("Ви отримали {$xpGained} exp!");

            $this->dispatch('characterUpdated');

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
            $this->addLogMessage("<span class='text-blue-800'>{$monster['name']} вдарив вас на {$monster['damage']} HP.</span>");
        } else {
            // Якщо монстр не потрапив
            $this->addLogMessage("<span class='text-green-600'>{$monster['name']} промахнувся.</span>");
        }

        // Якщо гравець мертвий
        if ($this->character['health'] <= 0) {
            $this->addLogMessage('Ви загинули!');
            $this->inBattle = false;
            $this->dispatch('showReviveButton');
            $this->showReviveModal = true;
        }

        $this->updateCharacterInDatabase();

        $this->dispatch('characterUpdated');
    }

    public function levelUp()
    {
        $this->character['level']++;
        $this->character['skill_points']++;
        $this->updateCharacterInDatabase();

    }

// Оновлення персонажа в базі даних
    protected function updateCharacterInDatabase()
    {
        $character = Character::find($this->character['id']);
        if ($character) {
            // Оновлення атрибутів
            $character->health = max(0, $this->character['health']); // Не даємо значенню бути менше 0
            $character->experience = $this->experience;
            $character->skill_points = $this->character['skill_points'];
            $character->save(); // Зберігаємо в базу

        }
    }

// Обчислення досвіду для монстра
    public function calculateExperienceGain($character, $monster): int
    {
        $baseExp = $monster['experience'];
        $levelDifference = $monster['level'] - $character['level'];

        // Модифікатор для досвіду
        $modifier = $levelDifference > 0 ? 1 + ($levelDifference * 0.1) : max(0, 1 + ($levelDifference * 0.2));
        $expGain = (int) round($baseExp * $modifier);

        // Оновлюємо досвід персонажа
        $character = Character::find($character['id']);
        if ($character) {
            $character->experience += $expGain;
            $this->checkAndLevelUp($character); // Перевіряємо підвищення рівня
            $character->save(); // Зберігаємо зміни
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
        while ($character->experience >= $this->getRequiredExperienceForLevel($character->level + 1)) {
            $character->experience -= $this->getRequiredExperienceForLevel($character->level + 1);
            $character->level++;
            $character->skill_points++;

            // Логування рівня
            $this->addLogMessage("<span class='text-green-600'>Вітаю! Ви досягли рівня {$character->level}, також у вас {$character->skill_points} sp</span>");
        }

        $character->save(); // Збереження
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

    public function revive()
    {
        $character = Character::find($this->character['id']);
        if ($character) {

            $character->position_x = $this->reviveCoordinates['x'];
            $character->position_y = $this->reviveCoordinates['y'];
            $character->health = (int) round($character->max_health * 0.1);

            $character->offset_x = -5;
            $character->offset_y = -4;

            $character->save();

            $this->offsetX = $character->offset_x;
            $this->offsetY = $character->offset_y;

            $this->dispatch('updateCharacterPosition', $this->characterX, $this->characterY);

            $this->showReviveModal = false;

            $this->dispatch('characterUpdated');
        }
    }

    public function removeMonsterBorder()
    {
        $this->monsterAttacked = false;
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to(route('login'));
    }

    public function openStats()
    {
        $this->showStats = true;
    }

    public function closeStats()
    {
        $this->showStats = false;
    }

    public function openSchool()
    {
        $this->showSchool = true;
    }

    public function closeSchool()
    {
        $this->showSchool = false;
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
            'objects' => $this->objects,
        ]);
    }
}
