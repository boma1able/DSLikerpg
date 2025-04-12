<?php

namespace App\Livewire;

use Livewire\Component;

class MapObjects extends Component
{
    public $objects = [];
    public $object;

    public function mount($object)
    {
        $this->object = $object;

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

        $this->objects[] = [
            'name' => 'Банк',
            'position_x' => 7,
            'position_y' => 5,
            'type' => 'bank',
        ];
    }

    public function render()
    {
        return view('livewire.map-objects');
    }
}
