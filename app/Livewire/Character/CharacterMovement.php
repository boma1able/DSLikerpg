<?php

namespace App\Livewire\Character;

use App\Services\MonsterEncounterService;
use Livewire\Component;

class CharacterMovement extends Component
{
    public array $map;
    public int $lastMoveTime = 0;
    public $characterX = 5;
    public $characterY = 5;

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
    ];

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

            // Виведення нової позиції персонажа
            $this->dispatch('characterMoved', $this->characterX, $this->characterY);

            // Виведення нового зміщення мапи
            $this->dispatch('updateMap', $this->map, $this->offsetX, $this->offsetY);

//            dd($this->monsters);
            // 🔥 Перевірка наявності монстрів та вивід повідомлення
            $monstersHere = collect($this->monsters)
                ->filter(fn($m) => (int)$m['position_x'] === (int)$this->characterX && (int)$m['position_y'] === (int)$this->characterY);

            if ($monstersHere->isNotEmpty()) {
                foreach ($monstersHere as $monster) {
                    $message = MonsterEncounterService::getMessage($monster['name']);
                    $this->addLogMessage($message);
                }
            }
        }
    }

    public function addLogMessage($message)
    {
        $currentTime = now()->format('H:i:s');
        $this->log[] = "<span class='inline-block w-[45px] text-[#a0a0a0] mr-2 text-xs'>{$currentTime}</span>{$message}";
        $this->dispatch('logUpdated', $this->log);
    }

    // Функція для обчислення зсуву
    private function calculateOffset()
    {
        $this->offsetX = -($this->characterX * 1);
        $this->offsetY = -($this->characterY * 1);
    }

    public function moveByKey($key)
    {
        switch ($key) {
            case 'ArrowUp':
                $this->move('up');
                break;
            case 'ArrowDown':
                $this->move('down');
                break;
            case 'ArrowLeft':
                $this->move('left');
                break;
            case 'ArrowRight':
                $this->move('right');
                break;
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
