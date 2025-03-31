@php
    $slots = [
        'helmet' => 'Шолом',
        'necklace' => 'Намисто',
        'cloak' => 'Плащ',
        'gloves' => 'Перчатки',
        'chest' => 'Броня',
        'ring' => 'Каблучка',
        'amulet' => 'Амулет',
        'weapon' => 'Зброя',
        'shield' => 'Щит',
        'belt' => 'Пояс',
        'leggings' => 'Штани',
        'boots' => 'Боти',
    ];

    $inventorySlots = [
        'helmet' => 'Шолом',
        'necklace' => 'Намисто',
        'cloak' => 'Плащ',
        'gloves' => 'Перчатки',
        'chest' => 'Броня',
        'ring' => 'Каблучка',
        'amulet' => 'Амулет',
        'weapon' => 'Зброя',
        'shield' => 'Щит',
        'belt' => 'Пояс',
        'leggings' => 'Штани',
        'boots' => 'Боти',
        'potion' => 'Зілля',
        'ingredient' => 'Інгрідієнти',
    ];
@endphp

<div class="flex">
    <div x-data class="flex w-auto">

        <!-- Область екіпірування -->
        <div class="grid grid-cols-3 gap-y-4 w-70">
            @foreach ($slots as $slot => $label)
                <div id="dropzone-{{ $slot }}"
                     class="dropzone border w-20 h-20 item"
                     data-allowed-type="{{ $slot }}">
                    @foreach ($equipment->where('slot', $slot) as $eq)
                        <div id="dropped-item-{{ $eq->item->id }}"
                             data-instance-id="{{ $eq->instance_id ?? '' }}"
                             data-item-id="{{ $eq->item->id }}"
                             class="relative sortable-item cursor-pointer group equipped-item">
                            <img src="{{ $eq->item->image }}" alt="">
                            <div class="absolute w-auto left-full top-1 border border-gray-400 bg-white p-2 text-xs z-[-1] group-hover:z-1 invisible group-hover:visible pointer-events-none">
                                <span class="block text-[10px] text-gray-400 text-center">{{ $eq->item->rarity }}</span>
                                <span class="block text-center">{{ $eq->item->name }}</span>
                                <span class="whitespace-nowrap"><span class="text-gray-400">Тип предмету:</span> {{ $label }}</span> <br>
                                @if ($eq->item->type === 'weapon')
                                    <span><span class="text-gray-400">Шкода:</span> 1-3</span>
                                @elseif ($eq->item->type !== 'weapon')
                                    <span><span class="text-gray-400">Броня:</span> 10</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Інвентар -->
        <div class="w-auto h-102">
            <div id="sortable-list" class="flex flex-wrap justify-start content-start gap-0 p-1 w-102 h-full bg-gray-300 bg-cover">
                @foreach ($items as $item)
                    <div id="item-{{ $item->id }}"
                         class="block relative sortable-item w-20 h-20 p-1 cursor-pointer group inventory-item"
                         data-instance-id="{{ $item->pivot->instance_id }}"
                         data-item-id="{{ $item->id }}"
                         data-type="{{ $item->type }}">
                        <img src="{{ $item->image }}" alt="">
{{--                    {{dd($item)}}--}}
                        <!-- Оновлена частина для кількості -->
                        @if ($item->stackable)
                            <div class="absolute top-0 right-0 bg-gray-600 text-white text-xs p-1 rounded">
                                x{{ $item->pivot->quantity }}  <!-- Показуємо кількість для stackable предметів -->
                            </div>
                        @endif

                        <div class="absolute w-auto left-full top-1 border border-gray-400 bg-white p-2 text-xs z-[-1] group-hover:z-1 invisible group-hover:visible pointer-events-none">
                            <span class="block text-[10px] text-gray-400 text-center">{{ $item->rarity }}</span>
                            <span class="block text-center">{{ $item->name }}</span>
                            <span class="whitespace-nowrap"><span class="text-gray-400">Тип предмету:</span> {{ $inventorySlots[$item->type] ?? 'Невідомий тип' }}</span> <br>
                            @if ($item->type === 'weapon')
                                <span><span class="text-gray-400">Шкода:</span> 1-3</span>
                            @endif
                            @if ($item->type !== 'weapon' && $inventorySlots[$item->type] !== 'Інгрідієнти' && $inventorySlots[$item->type] !== 'Зілля')
                                <span><span class="text-gray-400">Броня:</span> 10</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


    </div>
    <style>
        .dropzone.item:nth-child(1) {
            grid-column: 2;
            margin-bottom: -50px;
        }

        .dropzone.item:last-child {
            grid-column: 2;
            grid-row: 7;
            margin-top: -30px;
        }

        .dropzone.item:nth-child(even):not(:last-child) {
            grid-column: 1;
            grid-row: calc((n + 2) / 2);
        }

        .dropzone.item:nth-child(odd):not(:first-child) {
            grid-column: 3;
            grid-row: calc((n + 1) / 2);
        }
    </style>

    <!-- Підключення SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>


    <script>
        document.addEventListener('livewire:initialized', function () {
            // Перетягування з інвентаря в екіпірування
            new Sortable(document.getElementById('sortable-list'), {
                group: 'shared',
                onEnd: function (event) {
                    let itemElement = event.item;
                    let itemId = itemElement.id.replace('item-', '');
                    let instanceId = itemElement.getAttribute('data-instance-id'); // Отримуємо instanceId
                    let itemType = itemElement.getAttribute('data-type');
                    let dropzone = event.to;
                    let allowedType = dropzone.getAttribute('data-allowed-type');

                    // Якщо тип предмета не підходить для слоту, повертаємо на місце
                    if (allowedType && itemType !== allowedType) {
                        event.from.appendChild(event.item); // Повертаємо предмет назад
                        return;
                    }

                    console.log('Dropped item ID:', itemId, 'Instance ID:', instanceId, 'Type:', itemType);

                    // Викликаємо метод Livewire для одягання предмета
                @this.call('handleDrop', itemId, instanceId);
                }
            });

            // Перетаскування предметів з екіпірування
            document.querySelectorAll('.dropzone').forEach(dropzone => {
                new Sortable(dropzone, {
                    group: 'shared',
                    onEnd: function (event) {
                        let itemId = event.item.id.replace('dropped-item-', '');
                        let instanceId = event.item.getAttribute('data-instance-id'); // Отримуємо instanceId

                        console.log('Unequipping item ID:', itemId, 'Instance ID:', instanceId);

                        // Викликаємо метод Livewire для зняття предмета з екіпірування
                    @this.call('unequipItem', itemId, instanceId);
                    }
                });
            });
        });

        document.addEventListener('livewire:load', function () {
            Livewire.on('item-dropped', (event) => {
                const itemId = event.itemId;
                const itemType = event.itemType;

                let listItem = document.createElement('div');
                listItem.id = 'dropped-item-' + itemId;
                listItem.classList.add('sortable-item');
                listItem.textContent = "Item " + itemId;

                let dropzone = document.querySelector(`[data-allowed-type="${itemType}"]`);
                if (dropzone) {
                    dropzone.appendChild(listItem);
                } else {
                    console.error("Dropzone for type", itemType, "not found!");
                }
            });

            Livewire.on('item-unequipped', (event) => {
                const itemId = event.itemId;

                let equippedItem = document.getElementById('dropped-item-' + itemId);
                if (equippedItem) {
                    equippedItem.remove();
                }

                let listItem = document.createElement('div');
                listItem.id = 'item-' + itemId;
                listItem.classList.add('sortable-item');
                listItem.textContent = "Item " + itemId;

                document.getElementById('sortable-list').appendChild(listItem);
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('dblclick', function (event) {
                let item = event.target.closest('.inventory-item, .equipped-item');
                if (!item) return;

                const itemId = item.getAttribute('data-item-id');
                const instanceId = item.getAttribute('data-instance-id'); // Отримуємо instanceId

                if (item.classList.contains('inventory-item')) {
                @this.call('handleDrop', itemId, instanceId); // Передаємо instanceId
                } else if (item.classList.contains('equipped-item')) {
                @this.call('unequipItem', itemId, instanceId); // Передаємо instanceId
                }
            });
        });
    </script>








</div>
