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
    <div x-data class="flex w-full">

        <!-- Область екіпірування -->
        <div class="grid grid-cols-3 gap-y-4 w-70">
            @foreach ($slots as $slot => $label)
                <div id="dropzone-{{ $slot }}"
                     class="dropzone border w-20 h-20 item"
                     data-allowed-type="{{ $slot }}">
                    @foreach ($equipment->where('slot', $slot) as $eq)
                        <div id="dropped-item-{{ $eq->item->id }}"
                             data-item-id="{{ $eq->item->id }}"
                             class="relative sortable-item cursor-pointer group equipped-item"
                             x-data="{ x: 0, y: 0 }"
                             @mousemove="x = $event.offsetX; y = $event.offsetY"
                        >
                            <img src="{{ $eq->item->image }}" alt="">
                            <div class="absolute w-auto left-[80%] top-[80%] border border-gray-400 bg-white p-2 text-xs transition-transform duration-0 opacity-100 z-[-1] group-hover:opacity-100 group-hover:z-1"
                                 :style="'left: ' + (x + 14) + 'px; top: ' + (y + 14) + 'px'"
                            >
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
            <div id="sortable-list" class="flex flex-wrap justify-start content-start gap-0 p-1 w-102 h-full bg-gray-300">
                @foreach ($items as $item)
                    <div id="item-{{ $item->id }}"
                         class="block relative sortable-item w-20 h-20 p-1 cursor-pointer group inventory-item"
                         data-item-id="{{ $item->id }}"
                         data-type="{{ $item->type }}"
                         x-data="{ x: 0, y: 0 }"
                         @mousemove="x = $event.offsetX; y = $event.offsetY"
                    >
                        <img src="{{ $item->image }}" alt="">
                        <div class="absolute w-auto left-[80%] top-[80%] border border-gray-400 bg-white p-2 text-xs transition-transform duration-0 opacity-100 z-[-1] group-hover:opacity-100 group-hover:z-1"
                             :style="'left: ' + (x + 20) + 'px; top: ' + (y + 20) + 'px'"
                        >
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
                    let itemType = itemElement.getAttribute('data-type');
                    let dropzone = event.to;
                    let allowedType = dropzone.getAttribute('data-allowed-type');

                    // if (allowedType && itemType !== allowedType) {
                    //     alert('Цей предмет не підходить для цього слоту!');
                    //     return;
                    // }

                    console.log('Dropped item ID:', itemId, itemType);
                @this.call('handleDrop', itemId);
                }
            });

            // Автоматичне додавання Sortable для всіх drop-зон
            document.querySelectorAll('.dropzone').forEach(dropzone => {
                new Sortable(dropzone, {
                    group: 'shared',
                    onEnd: function (event) {
                        let itemId = event.item.id.replace('dropped-item-', '');
                        console.log('Unequipping item ID:', itemId);
                    @this.call('unequipItem', itemId);
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
                if (!item) return; // Якщо клік не по предмету – ігноруємо

                const itemId = item.getAttribute('data-item-id');

                if (item.classList.contains('inventory-item')) {
                @this.call('handleDrop', itemId);
                } else if (item.classList.contains('equipped-item')) {
                @this.call('unequipItem', itemId);
                }
            });
        });
    </script>





</div>
