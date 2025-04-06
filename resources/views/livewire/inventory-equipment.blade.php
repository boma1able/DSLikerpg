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
    <div class="flex w-auto">

        <!-- Область екіпірування -->
        <div class="grid grid-cols-3 gap-y-4 w-70">
            @foreach ($slots as $slot => $label)
                <div id="dropzone-{{ $slot }}"
                     class="dropzone border w-20 h-20 item"
                     data-allowed-type="{{ $slot }}">
                    @foreach ($equipment->where('slot', $slot) as $eq)
                        <div id="dropped-item-{{ $eq->item->id }}-{{ $eq->instance_id }}"
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

        <!-- Інвентар  inv-grid.png-->
        <div class="w-auto h-102">
            <div id="sortable-list" class="relative flex flex-wrap justify-start content-start gap-x-1 p-1 w-102 h-full bg-gray-300 bg-cover"
            style="background: url({{ asset('storage/ui/inv-grid.png') }}); background-position: 0 0, 46px 0, 0 46px, 46px 46px; ">
                @foreach ($items as $item)
                    @if ($item->pivot)
                        <div id="item-{{ $item->id }}-{{ $item->pivot->instance_id }}"
                         class="block relative sortable-item w-20 h-20 cursor-pointer group inventory-item"
                         data-instance-id="{{ $item->pivot->instance_id }}"
                         data-item-id="{{ $item->id }}"
                         data-type="{{ $item->type }}">
                        <img src="{{ $item->image }}" alt="">
                        @if ($item->stackable)
                            <div class="absolute top-0 right-0 bg-gray-600 text-white text-xs p-1 rounded">
                                x{{ $item->pivot->quantity }}
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
                    @endif
                @endforeach
            </div>

            <div id="trash-bin" class="trash-bin dropzone w-20 h-20 border">
                Перетягніть сюди, щоб видалити
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
        .dropzone,
        #trash-bin{
            position: relative;
        }
        .dropzone>div:nth-child(2),
        #trash-bin>div{
            position: absolute;
            top: 0;
            left: 0;
            padding: 0;
        }
    </style>

    <!-- Підключення SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>


    <script>
        document.addEventListener('livewire:initialized', function () {
            // Перетаскування предметів з інвентаря
            new Sortable(document.getElementById('sortable-list'), {
                group: 'shared',
                onEnd: function (event) {
                    let itemElement = event.item;
                    let itemId = itemElement.id;
                    let instanceId = null;

                    // Перевіряємо, чи правильно визначений ID
                    if (itemId.startsWith('item-')) {
                        let parts = itemId.replace('item-', '').split('-');
                        itemIdValue = parts[0];
                        instanceId = parts.slice(1).join('-');
                    } else {
                        return; // Якщо itemId некоректний
                    }

                    // Збираємо інформацію про тип елемента та зону
                    let itemType = itemElement.getAttribute('data-type');
                    let dropzone = event.to;

                    // Якщо предмет перетягнуто в площу видалення
                    if (dropzone.id === 'trash-bin') {
                        console.log('Item is being deleted:', itemIdValue, 'Instance ID:', instanceId);

                        // Викликаємо метод Livewire для видалення
                    @this.call('deleteItem', itemIdValue, instanceId).then(() => {
                        // Оновлюємо інвентар на фронті
                        updateInventoryOnFront(itemIdValue, instanceId);
                        console.log('Item has been deleted from inventory (front-end update).');
                    }).catch(error => {
                        console.error('Error deleting item:', error);
                    });

                    } else {
                        // Якщо предмет не в зоні видалення, обробляємо як перетаскування в інший слот екіпірування
                        console.log('Item is being equipped:', itemIdValue, 'Instance ID:', instanceId);

                        // Викликаємо метод Livewire для екіпірування
                    @this.call('handleDrop', itemIdValue, instanceId);
                        updateInventoryOnFront(itemIdValue, instanceId);
                    }
                }
            });

            // Перетаскування предметів з екіпірування
            document.querySelectorAll('.dropzone').forEach(dropzone => {
                new Sortable(dropzone, {
                    group: 'shared',
                    onEnd: function (event) {
                        let itemElement = event.item;
                        let itemIdWithInstance = itemElement.id.replace('dropped-item-', '');

                        if (!itemIdWithInstance) {
                            return; // Якщо некоректний ID
                        }

                        let parts = itemIdWithInstance.split('-');
                        let itemId = parts[0];
                        let instanceId = parts.slice(1).join('-');

                        if (!instanceId) {
                            return; // Якщо відсутній instanceId
                        }

                        // Викликаємо метод Livewire для зняття екіпірування
                    @this.call('unequipItem', itemId, instanceId);
                        updateInventoryOnFront(itemId, instanceId);
                    }
                });
            });
        });

        // Функція для оновлення інвентаря на фронті
        function updateInventoryOnFront(itemId, instanceId) {
            let inventoryItem = document.querySelector(`#item-${itemId}-${instanceId}`);
            let equippedItem = document.querySelector(`#dropped-item-${itemId}-${instanceId}`);

            // Оновлюємо інвентар (якщо предмет знайдено)
            if (inventoryItem) {
                let newItem = document.createElement('div');
                newItem.id = `item-${itemId}-${instanceId}`;
                newItem.classList.add('sortable-item');
                newItem.textContent = `Item ${itemId}`;

                newItem.setAttribute('data-instance-id', instanceId);
                newItem.setAttribute('data-item-id', itemId);
                newItem.setAttribute('data-type', 'weapon');  // Змініть на реальний тип

                inventoryItem.replaceWith(newItem); // Заміна інвентаря
            }

            // Оновлюємо екіпірування (якщо предмет знайдено)
            if (equippedItem) {
                let newEquippedItem = document.createElement('div');
                newEquippedItem.id = `dropped-item-${itemId}-${instanceId}`;
                newEquippedItem.classList.add('sortable-item');
                newEquippedItem.textContent = `Item ${itemId}`;

                newEquippedItem.setAttribute('data-instance-id', instanceId);
                newEquippedItem.setAttribute('data-item-id', itemId);
                newEquippedItem.setAttribute('data-type', 'weapon');  // Змініть на реальний тип

                equippedItem.replaceWith(newEquippedItem); // Заміна предмета в екіпіруванні
            }

            if (inventoryItem) {
                inventoryItem.remove();
                console.log('Item removed from DOM.');
            } else {
                console.log('Item not found in DOM.');
            }
        }
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
