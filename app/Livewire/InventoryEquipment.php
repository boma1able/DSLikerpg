<?php

namespace App\Livewire;

use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Item;
use App\Models\Equipment;
use App\Models\Character;

class InventoryEquipment extends Component
{
    public $equipment;
    public $user;
    public $items = [];

    protected $listeners = [
        'inventoryUpdated' => 'loadEquipment'
    ];

    public function mount()
    {
        $this->user = auth()->user();
        $this->equipment = $this->user->equipment ?? collect();

        $this->loadEquipment();

        if ($this->user && $this->user->inventory) {
            $this->items = $this->user->inventory->items;
        } else {
            $this->items = collect();  // Пустий інвентар, якщо його немає
        }
    }

    public function loadEquipment()
    {
        $this->equipment = Equipment::where('user_id', $this->user->id)
            ->where('character_id', $this->user->character->id)
            ->with('item') // Завантажуємо сам предмет
            ->get();

        $inventory = auth()->user()->inventory;

        if ($inventory) {
            // Завантажуємо предмети з інвентаря
            $this->items = $inventory->items;
        } else {
            $this->items = collect(); // Якщо інвентар не знайдений
        }
    }


    public function handleDrop($itemId, $instanceId)
    {
        $characterId = $this->user->character->id;

        // Отримуємо предмет з інвентаря разом із пивотними даними, враховуючи instance_id
        $item = $this->user->inventory->items()
            ->where('item_id', $itemId)
            ->wherePivot('instance_id', $instanceId) // Фільтруємо за instance_id
            ->first();

        if (!$item || !$item->pivot || !$item->pivot->instance_id) {
            return; // Якщо предмет чи пивот або instance_id не існують
        }

        // Масив дозволених слотів
        $allowedSlots = ['helmet', 'weapon', 'chest', 'cloak', 'shield', 'gloves', 'leggings', 'boots', 'belt', 'ring', 'amulet', 'necklace'];

        // Перевіряємо, чи тип предмета входить у дозволені слоти
        if (!in_array($item->type, $allowedSlots)) {
            return;
        }

        // Додаємо предмет в екіпіровку
        Equipment::create([
            'user_id' => $this->user->id,
            'character_id' => $characterId,
            'item_id' => $item->id,
            'instance_id' => $item->pivot->instance_id, // Вже гарантовано є
            'slot' => $item->type,
        ]);

        // Якщо предмет стекується, можемо зменшити кількість
        if ($item->stackable && $item->pivot->quantity > 1) {
            $this->user->inventory->items()->updateExistingPivot($item->id, [
                'quantity' => $item->pivot->quantity - 1,
            ]);
        } else {
            $this->user->inventory->items()->wherePivot('instance_id', $instanceId)->detach($item->id);
        }

        // Оновлюємо списки
        $this->loadEquipment();
        $this->items = $this->user->inventory->items;

        // Відправляємо подію для оновлення UI
        $this->dispatch('itemEquipped', $item->id);
    }


    public function unequipItem($itemId, $instanceId)
    {
        // Перевіряємо наявність предмета в екіпіруванні з потрібним item_id і instance_id
        $equipment = Equipment::where('user_id', $this->user->id)
            ->where('character_id', $this->user->character->id)
            ->where('item_id', $itemId)
            ->where('instance_id', $instanceId) // Фільтруємо за instance_id
            ->first();

        if ($equipment) {
            // Видаляємо предмет з екіпірування
            $equipment->delete();

            $this->user->inventory->items()->attach($itemId, ['instance_id' => $instanceId]);

            // Оновлюємо списки
            $this->items = $this->user->inventory->items;
            $this->equipment = Equipment::where('user_id', $this->user->id)
                ->where('character_id', $this->user->character->id)
                ->get();

            // Відправляємо подію у фронт
            $this->dispatch('itemUnequipped', ['itemId' => $itemId, 'instanceId' => $instanceId]);
        } else {
            $this->dispatch('error', 'Предмет не знайдений у екіпіруванні');
        }
    }




    public function render()
    {
        return view('livewire.inventory-equipment');
    }
}
