<?php

namespace App\Livewire;

use Livewire\Component;

class ShowMapImages extends Component
{
    public $characterX = 1;
    public $characterY = 1;

    protected $listeners = [
        'characterMoved' => 'updateCoordinates',
    ];

    public function updateCoordinates($newX, $newY)
    {
        $this->characterX = $newX;
        $this->characterY = $newY;
    }

    public function getImagePath()
    {
        return asset('storage/mapImages/map_' . $this->characterX . '_y' . $this->characterY . '.jpg');
    }

    public function getStepExcerpt()
    {
        $key = $this->characterX . '_' . $this->characterY;
        return config("steps.$key", 'Невідома місцевість.');
    }

    public function render()
    {
        return view('livewire.show-map-images');
    }
}
