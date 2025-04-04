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
    public $log = [];

    protected $listeners = [
        'addLogMessage',
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
            $this->items = collect();
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
            $this->items = $inventory->items;
        } else {
            $this->items = collect();
        }
    }


    public function handleDrop($itemId, $instanceId)
    {
        $characterId = $this->user->character->id;

        $item = $this->user->inventory->items()
            ->where('item_id', $itemId)
            ->wherePivot('instance_id', $instanceId)
            ->first();

        if (!$item || !$item->pivot || !$item->pivot->instance_id) {
            return;
        }

        // Масив дозволених слотів
        $allowedSlots = ['helmet', 'weapon', 'chest', 'cloak', 'shield', 'gloves', 'leggings', 'boots', 'belt', 'ring', 'amulet', 'necklace'];

        // Перевіряємо, чи тип предмета входить у дозволені слоти
        if (!in_array($item->type, $allowedSlots)) {
            return;
        }

        // Перевірка чи є вже предмет в цьому слоті
        $existingEquipment = Equipment::where('user_id', $this->user->id)
            ->where('character_id', $characterId)
            ->where('slot', $item->type)
            ->first();

        if ($existingEquipment) {
            // Якщо предмет є, то знімаємо його та повертаємо в інвентар
            $this->unequipItem($existingEquipment->item_id, $existingEquipment->instance_id);
        }

        // Додаємо новий предмет в екіпіровку
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
            // Видаляємо предмет з інвентаря
            $this->user->inventory->items()->wherePivot('instance_id', $instanceId)->detach($item->id);
        }

        // Оновлюємо екіпіровку
        $this->loadEquipment();

        // Оновлюємо інвентар
        $this->loadItems(); // Переконайтеся, що у вас є метод для завантаження інвентаря

        // Відправляємо подію для оновлення UI
        $this->dispatch('itemEquipped', $item->id);
    }

    public function loadItems()
    {
        $inventory = auth()->user()->inventory;
        if ($inventory) {
            $this->items = $inventory->items;
        } else {
            $this->items = collect();
        }
    }

    public function unequipItem($itemId, $instanceId)
    {
        $equipment = Equipment::where('user_id', $this->user->id)
            ->where('character_id', $this->user->character->id)
            ->where('item_id', $itemId)
            ->where('instance_id', $instanceId)
            ->first();

        if ($equipment) {
            $equipment->delete();
            $this->user->inventory->items()->attach($itemId, ['instance_id' => $instanceId]);

            $this->items = $this->user->inventory->items;
            $this->equipment = Equipment::where('user_id', $this->user->id)
                ->where('character_id', $this->user->character->id)
                ->get();

            $this->dispatch('itemUnequipped', ['itemId' => $itemId, 'instanceId' => $instanceId]);
        }
    }

//182	1	1	6251c205-c838-43e4-a57d-a4edd9afcb22	NULL	1	2025-03-31 14:07:45	2025-03-31 14:07:45

    public function deleteItem($itemIdValue, $instanceId)
    {
        $user = auth()->user();

        if (!$user->inventory) {
            return;
        }

        $item = $user->inventory->items()
            ->wherePivot('instance_id', $instanceId)
            ->wherePivot('item_id', $itemIdValue)
            ->first();

        if (!$item) {
            return;
        }

        $user->inventory->items()->wherePivot('instance_id', $instanceId)->detach();

        $itemName = $item->name;
        $message = "<span class='text-gray-400'>Ви викинули $itemName з інвентаря!</span>";
        $this->dispatch('logMessage', $message);
        $this->loadEquipment();
    }


    public function render()
    {
        return view('livewire.inventory-equipment');
    }
}
