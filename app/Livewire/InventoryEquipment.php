<?php

namespace App\Livewire;

use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Item;
use App\Models\Equipment;
use App\Models\Character;

class InventoryEquipment extends Component
{
    public $equipment;
    public $user;
    public $items = [];

    public function mount()
    {
        $this->user = auth()->user();
        $this->equipment = $this->user->equipment ?? collect();

        $this->loadEquipment();

        if ($this->user && $this->user->inventory) {
            // Завантажуємо items для інвентаря
            $this->user->inventory->load('items');
            $this->items = $this->user->inventory->items;
        } else {
            $this->items = [];
        }
    }

    public function loadEquipment()
    {
        $this->equipment = Equipment::where('user_id', $this->user->id)
            ->where('character_id', $this->user->character->id)
            ->with('item') // Завантажуємо пов'язані предмети
            ->get();
    }

    // Додайте новий слот для шолома в методі handleDrop
    public function handleDrop($itemId)
    {
        $item = Item::find($itemId);
        $characterId = $this->user->character->id;

        if (!$item) {
            return;
        }

        // Масив дозволених слотів
        $allowedSlots = ['helmet', 'weapon', 'chest', 'cloak', 'shield', 'gloves', 'leggings', 'boots', 'belt', 'ring', 'amulet', 'necklace', 'ingredient'];

        // Перевіряємо, чи тип предмета входить у дозволені слоти
        if (!in_array($item->type, $allowedSlots)) {
            return;
        }

        // Додаємо в еквіпмент
        Equipment::create([
            'user_id' => $this->user->id,
            'character_id' => $characterId,
            'item_id' => $item->id,
            'slot' => $item->type, // Використовуємо напряму
        ]);

        // Видаляємо предмет з інвентаря
        $this->user->inventory->items()->detach($item->id);

        // Оновлюємо списки
        $this->loadEquipment();
        $this->items = $this->user->inventory->items;

        // Відправляємо подію для оновлення UI
        $this->dispatch('itemEquipped', $item->id);
    }


    public function unequipItem($itemId)
    {
        $equipment = Equipment::where('user_id', $this->user->id)
            ->where('character_id', $this->user->character->id)
            ->where('item_id', $itemId)
            ->first();

        if ($equipment) {
            // Видаляємо предмет з екіпірування
            $equipment->delete();

            // Додаємо назад у інвентар
            $this->user->inventory->items()->attach($itemId);

            // Оновлюємо списки
            $this->items = $this->user->inventory->items;
            $this->equipment = Equipment::where('user_id', $this->user->id)
                ->where('character_id', $this->user->character->id)
                ->get();

            // Відправляємо подію у фронт
            $this->dispatch('item-unequipped', ['itemId' => $itemId]);
        }
    }

    public function render()
    {
        return view('livewire.inventory-equipment');
    }
}
