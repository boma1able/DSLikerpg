<?php

namespace App\Livewire\Character;

use App\Services\MonsterEncounterService;
use Livewire\Component;

class CharacterMovement extends Component
{
    public array $map;
    public int $lastMoveTime = 0;
    public $characterX = 5;
    public $characterY = 4;

    public int $offsetX = 0;
    public int $offsetY = 0;

    public array $log = [];

    public bool $isResting = false;
    public bool $restStarted = false;
    public bool $hasRestingInterruptedMessage = false;

    public array $monsters = [];

    protected $listeners = [
        'updateCharacterPosition',
        'addLogMessage',
        'monstersUpdated',
    ];

    public function mount()
    {
        $character = auth()->user()->character;
        $this->characterX = $character->position_x;
        $this->characterY = $character->position_y;

        $this->calculateOffset();
    }

    public function monstersUpdated($monsters)
    {
        $this->monsters = $monsters;
    }

    public function monsterMoved($monsterId, $newX, $newY)
    {
        foreach ($this->monsters as $monster) {
            if ($monster['id'] == $monsterId) {
                // Оновлюємо позицію монстра
                $monster['position_x'] = $newX;
                $monster['position_y'] = $newY;
            }
        }
    }


    public function updateCharacterPosition($x, $y)
    {
        $this->characterX = $x;
        $this->characterY = $y;
    }

    public function move($direction)
    {
        $currentTime = time();

        // Якщо персонаж рухається, припиняємо відновлення
        if ($this->isResting) {
            $this->isResting = false;
            $this->restStarted = false;
            $this->hasRestingInterruptedMessage = true;
            $this->addLogMessage('⛔ Відновлення перервано!');
            $this->dispatch('stopResting');
        }

        // Перевірка, чи минуло достатньо часу між рухами
        if ($currentTime - $this->lastMoveTime < 1) {
            return;
        }

        $this->lastMoveTime = $currentTime;

        // Підготовка нових координат
        $newX = $this->characterX;
        $newY = $this->characterY;

        // Дозволені напрямки для кожної плитки
        $allowedDirections = [
            '2' => ['up', 'down', 'left', 'right'],
            '3' => ['up', 'down'],
            '5' => ['up', 'right'],
            '6' => ['down', 'right'],
            '7' => ['left', 'right'],
            '8' => ['down', 'left', 'right'],
            '9' => ['down', 'left'],
            '10' => ['up', 'left'],
            '11' => ['up', 'down', 'left'],
            '13' => ['up', 'left', 'right'],
            '14' => ['left'],
            '15' => ['up', 'down', 'right'],
        ];

        // Поточна плитка, на якій стоїть персонаж
        if (!isset($this->map[$this->characterY][$this->characterX])) {
            $this->addLogMessage("❌ Неможливо визначити наступний крок для руху!");
            return;
        }
        $currentTile = $this->map[$this->characterY][$this->characterX] ?? null;

        // Перевірка на допустимість руху
        if (!in_array($direction, $allowedDirections[$currentTile] ?? [])) {
            $this->addLogMessage("❌ Рух у цьому напрямку заборонено!");
            return;
        }

        // Оновлення координат
        match ($direction) {
            'up' => $newY -= 1,
            'down' => $newY += 1,
            'left' => $newX -= 1,
            'right' => $newX += 1,
        };

        // Перевірка нової позиції
        if (isset($this->map[$newY][$newX]) && $this->map[$newY][$newX] !== 'x') {
            // Оновлення позиції персонажа
            $this->characterX = $newX;
            $this->characterY = $newY;

            // Оновлення зміщення мапи
            $this->calculateOffset();

            // Збереження нових координат у базу
            $character = auth()->user()->character;
            $character->position_x = $this->characterX;
            $character->position_y = $this->characterY;
            $character->offset_x = $this->offsetX;
            $character->offset_y = $this->offsetY;

            $character->save();

            // Виведення нової позиції персонажа
            $this->dispatch('characterMoved', $this->characterX, $this->characterY);

            // Виведення нового зміщення мапи
            $this->dispatch('updateMap', $this->map, $this->offsetX, $this->offsetY);

            $this->dispatch('monstersUpdated', $this->monsters);

            // Перевірка на зіткнення
            foreach ($this->monsters as $monster) {
                if ($monster['position_x'] === $this->characterX && $monster['position_y'] === $this->characterY) {
                    $message = MonsterEncounterService::getMessage($monster['name']);
                    $this->addLogMessage("<span class='text-gray-400'>$message</span>");
                }
            }
        }
    }

    public function getCurrentTime()
    {
        return now()->format('H:i:s');
    }

    public function addLogMessage($message)
    {
        $this->log[] = "<span class='inline-block w-[45px] text-[#a0a0a0] mr-2 text-xs'>{$this->getCurrentTime()}</span>{$message}";
        $this->dispatch('logUpdated', $this->log);
    }

    // Функція для обчислення зсуву
    private function calculateOffset()
    {
        $this->offsetX = -$this->characterX;
        $this->offsetY = -$this->characterY;
    }

    public function moveByKey($key)
    {
        $directions = [
            'ArrowUp' => 'up',
            'ArrowDown' => 'down',
            'ArrowLeft' => 'left',
            'ArrowRight' => 'right'
        ];

        if (isset($directions[$key])) {
            $this->move($directions[$key]);
        }
    }


    public function resting($key)
    {
        if ($key === 'z') {
            $this->startResting();
        }
    }

    public function startResting()
    {
        $this->isResting = true;
        $this->hasRestingInterruptedMessage = false;

        $this->dispatch('restStarted', ['isResting' => $this->isResting]);
    }

    public function render()
    {
        return view('livewire.character.character-movement');
    }
}
