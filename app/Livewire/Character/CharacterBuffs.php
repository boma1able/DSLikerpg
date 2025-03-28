<?php

namespace App\Livewire\Character;

use App\Jobs\DeactivateBuffJob;
use App\Models\Character;
use App\Models\CharacterBuff;
use Livewire\Component;
use App\Services\CharacterBuffService;

class CharacterBuffs extends Component
{
    public $character;
    public $buffs;
    public $usedHotkeys = [];
    public $selectedBuffId = null;
    public $hotkeyMenuOpen = false;

    protected $listeners = [
        'activateBuffByKey',
        'schoolUpdated' => 'updateSchool',
        'buffAdded' => 'refreshBuffs',
    ];

    public function mount(Character $character)
    {
        $this->character = $character;
        $this->refreshBuffs();
        $this->loadBuffs();
    }

    public function updateSchool($totalHealth, $totalDamage, $totalMagicDamage, $totalArmor, $totalMana)
    {
        // Оновлюємо атрибути персонажа
        $this->character->max_health = $totalHealth;
        $this->character->max_mana = $totalMana;
        $this->character->damage = $totalDamage;
        $this->character->magic_damage = $totalMagicDamage;
        $this->character->armor = $totalArmor;
    }

    public function refreshBuffs()
    {
        $this->buffs = $this->character->buffs;
        $this->usedHotkeys = $this->buffs->pluck('hotkey')->filter()->toArray();
    }

    public function openHotkeyMenu($buffId)
    {
        $this->selectedBuffId = $buffId;
        $this->hotkeyMenuOpen = true;
    }

    public function closeHotkeyMenu()
    {
        $this->hotkeyMenuOpen = false;
        $this->selectedBuffId = null;
    }

    public function setHotkey($buffId, $key)
    {
        $buff = $this->buffs->where('id', $buffId)->first();

        if ($buff) {
            // Якщо кнопка не була вже вибрана
            if (!in_array($key, $this->usedHotkeys)) {
                $buff->hotkey = $key;
                $buff->save();

                // Додаємо кнопку до списку використаних
                $this->usedHotkeys[] = $key;

                // Оновлюємо бафи
                $this->refreshBuffs();
            }

            // Закриваємо контекстне меню після вибору кнопки
            $this->closeHotkeyMenu();
        }
    }

    public function activateBuffByKey($key)
    {
        $buff = $this->buffs->firstWhere('hotkey', $key);

        if ($buff) {
            $this->activateBuff($buff->id);
        }
    }

    public function activateBuff($buffId)
    {
        $buff = $this->buffs->where('id', $buffId)->first();

        if ($buff) {
            $buff->is_active = 1;
            $buff->applied_at = now();
            $buff->save();

            $character = auth()->user()->character;

            CharacterBuffService::updateCharacterBuffs($character);

            $this->dispatch('buffActivated', $buffId);

            DeactivateBuffJob::dispatch($buff->id)->delay(now()->addMinutes(.5));

            $this->refreshBuffs();
        }
    }

    public function loadBuffs()
    {
        $this->buffs = CharacterBuff::where('character_id', auth()->id())->get();

        $this->dispatch('updateBuffsInCard', $this->buffs);
    }


    public function render()
    {
        return view('livewire.character.character-buffs', [
            'buffs' => $this->buffs,
        ]);
    }
}
