<?php

namespace App\Livewire;

use App\Models\Character;
use Livewire\Component;

class BankManager extends Component
{
    public $characterId;
    public $gold;
    public $bankGold;

    public $amount = 0;

    protected $listeners = ['goldUpdated' => 'loadGold'];

    public function mount($characterId)
    {
        $this->characterId = $characterId;

        $character = Character::find($this->characterId);
        if ($character) {
            $this->gold = $character->gold;
            $this->bankGold = $character->bank_gold;
        }
    }

    public function loadGold()
    {
        $character = Character::find($this->characterId);
        if ($character) {
            $this->gold = $character->gold;
            $this->bankGold = $character->bank_gold;
        }
    }

    public function deposit()
    {
        $amount = (int)$this->amount;

        if ($amount > 0 && $amount <= $this->gold) {
            $character = Character::find($this->characterId);
            $character->gold -= $amount;
            $character->bank_gold += $amount;
            $character->save();

            $this->dispatch('goldUpdated', $character->gold, $character->bank_gold);
            $this->addMessage("Ви поклали $amount золота у банк.");
            $this->loadGold();
            $this->amount = 0;
        }
    }

    public function withdraw()
    {
        $amount = (int)$this->amount;

        if ($amount > 0 && $amount <= $this->bankGold) {
            $character = Character::find($this->characterId);
            $character->gold += $amount;
            $character->bank_gold -= $amount;
            $character->save();

            $this->dispatch('goldUpdated', $character->gold, $character->bank_gold);
            $this->addMessage("Ви зняли $amount золота з банку.");
            $this->loadGold();
            $this->amount = 0;
        }
    }

    public function addMessage($text)
    {
        $this->dispatch('logMessage', $text);
    }

    public function render()
    {
        return view('livewire.bank-manager');
    }
}
