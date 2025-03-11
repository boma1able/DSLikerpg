<?php

namespace App\Livewire;

use Livewire\Component;

class ShowMapImages extends Component
{
    public $characterX = 1; // Початкові координати
    public $characterY = 1;

    protected $listeners = [
        'characterMoved' => 'updateCoordinates',
    ];

    public function updateCoordinates($newX, $newY)
    {
        $this->characterX = $newX;
        $this->characterY = $newY;
    }

    // Метод для формування шляху до картинки на основі координат
    public function getImagePath()
    {
        return asset('storage/mapImages/map_' . $this->characterX . '_y' . $this->characterY . '.jpg');
    }

    public function render()
    {
        return view('livewire.show-map-images');
    }
}
