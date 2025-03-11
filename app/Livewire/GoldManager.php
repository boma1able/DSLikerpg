<?php
namespace App\Livewire;

use App\Models\Monster;
use Livewire\Component;
use App\Models\Character;

class GoldManager extends Component
{
    public $characterId;
    public $gold;

    protected $listeners = ['goldUpdated' => 'refreshGold'];

    public function mount($characterId)
    {
        $this->characterId = $characterId;

        $character = Character::find($this->characterId);
        if ($character) {
            $this->gold = $character->gold;
        }
    }

    public function refreshGold($goldAmount)
    {
        $this->gold = $goldAmount;
    }

    public function render()
    {
        return view('livewire.gold-manager');
    }
}
