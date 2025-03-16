<div class="flex">
    <!-- Інвентар -->
{{--    <div class="w-1/2 p-4 border rounded">--}}
{{--        <h2 class="text-lg font-bold mb-4">Інвентар</h2>--}}
{{--        <ul id="inventory-list" class="space-y-2">--}}
{{--            @foreach($inventory->items as $item)--}}
{{--                <li id="item-{{ $item->id }}" class="bg-gray-100 p-2 cursor-move" data-id="{{ $item->id }}" draggable="true">--}}
{{--                    <span>{{ $item->name }}</span>--}}
{{--                    <span class="text-sm text-gray-500">Тип: {{ $item->type }}</span>--}}
{{--                </li>--}}
{{--            @endforeach--}}
{{--        </ul>--}}

{{--    </div>--}}

{{--    <!-- Екіпіровка -->--}}
{{--    <div class="w-1/2 p-4 border rounded">--}}
{{--        <h2 class="text-lg font-bold mb-4">Екіпіровка</h2>--}}
{{--        <div id="equipment-slots" class="grid grid-cols-2 gap-4">--}}
{{--            @foreach($equipmentSlots as $slot)--}}
{{--                <div id="slot-{{ $slot }}" class="bg-gray-200 p-2 h-24 flex justify-center items-center border"--}}
{{--                     data-slot="{{ $slot }}"--}}
{{--                     style="cursor: pointer;">--}}

{{--                    @if(isset($equipment[$slot]))--}}
{{--                        <span>{{ $equipment[$slot]->item->name }}</span>--}}
{{--                    @else--}}
{{--                        <span>Порожньо</span>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--    </div>--}}

    <div x-data>
        <!-- Список предметів у інвентарі -->
        <ul id="sortable-list">
            @foreach ($items as $item)
                <li id="item-{{ $item->id }}" class="sortable-item m-1">
                    Назва: {{ $item->name }}<br>
                    Тип: {{ $item->type }}
                </li>
            @endforeach
        </ul>


        <!-- Дроп-зона для екіпірування -->
        <ul id="dropzone">
            @foreach ($equipment as $eq)
                <li id="dropped-item-{{ $eq->item->id }}" class="sortable-item">
                    {{ $eq->item->name }} (Slot: {{ $eq->slot }})
                </li>
            @endforeach
        </ul>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', function () {
            // Перетягування з інвентаря в екіпірування
            let sortable = new Sortable(document.getElementById('sortable-list'), {
                group: 'shared',
                onEnd: function (event) {
                    let itemId = event.item.id.replace('item-', '');
                    console.log('Equipping item ID:', itemId);
                @this.call('handleDrop', itemId);
                }
            });

            // Перетягування з екіпірування назад в інвентар
            new Sortable(document.getElementById('dropzone'), {
                group: 'shared',
                onEnd: function (event) {
                    let itemId = event.item.id.replace('dropped-item-', '');
                    console.log('Unequipping item ID:', itemId);
                @this.call('unequipItem', itemId);
                }
            });
        });

        document.addEventListener('livewire:load', function () {
            // Додаємо екіпірований предмет у дропзону
            Livewire.on('item-dropped', (event) => {
                const itemId = event.itemId;

                let listItem = document.createElement('li');
                listItem.id = 'dropped-item-' + itemId;
                listItem.classList.add('sortable-item');
                listItem.textContent = "Item " + itemId;

                document.getElementById('dropzone').appendChild(listItem);
            });

            // Видаляємо предмет з екіпірування і повертаємо його в інвентар
            Livewire.on('item-unequipped', (event) => {
                const itemId = event.itemId;

                let equippedItem = document.getElementById('dropped-item-' + itemId);
                if (equippedItem) {
                    equippedItem.remove();
                }

                let listItem = document.createElement('li');
                listItem.id = 'item-' + itemId;
                listItem.classList.add('sortable-item');
                listItem.textContent = "Item " + itemId;

                document.getElementById('sortable-list').appendChild(listItem);
            });
        });
    </script>





    <style>
        /* Стилі для dropzone */
        #dropzone {
            border: 2px solid blue;
            height: 50px;
        }

        /* Стилі для елементів списку */
        .sortable-item {
            padding: 5px;
            background-color: lightgray;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>


</div>


{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>--}}

{{--<script>--}}
{{--    document.addEventListener('DOMContentLoaded', function() {--}}
{{--        const inventoryList = document.getElementById('inventory-list');--}}

{{--        if (!inventoryList) {--}}
{{--            console.error("Element with id 'inventory-list' not found.");--}}
{{--            return;--}}
{{--        }--}}

{{--        new Sortable(inventoryList, {--}}
{{--            group: 'shared',--}}
{{--            draggable: 'li',--}}
{{--            onEnd(evt) {--}}
{{--                const itemId = evt.item.dataset.id;--}}
{{--                const slotId = evt.from.id;  // отримання ID слота--}}
{{--                // Викликаємо Livewire метод для обробки перетягування--}}
{{--            @this.call('equipFromInventory', itemId, slotId);--}}
{{--            }--}}
{{--        });--}}

{{--        // Окремо ініціалізуємо слот для перетягування--}}
{{--        document.querySelectorAll('#equipment-slots .bg-gray-200').forEach(slot => {--}}
{{--            slot.addEventListener('drop', function(event) {--}}
{{--                event.preventDefault();--}}
{{--                const itemId = event.dataTransfer.getData('item-id');--}}
{{--                const slotId = event.target.dataset.slot;--}}
{{--            @this.call('equipFromInventory', itemId, slotId);--}}
{{--            });--}}
{{--            slot.addEventListener('dragover', function(event) {--}}
{{--                event.preventDefault();--}}
{{--            });--}}
{{--        });--}}
{{--    });--}}

{{--</script>--}}
