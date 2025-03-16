<div class="border p-4">
    <h2 class="text-lg font-bold">🛡 Екіпіровка</h2>
    <div class="grid grid-cols-3 gap-2">
        @foreach($equipmentSlots as $slot)
            <div class="border p-4 text-center bg-gray-100"
                 @drop="Livewire.dispatch('equipItem', { inventoryId: event.dataTransfer.getData('inventoryId'), slot: '{{ $slot }}' })"
                 @dragover.prevent @dragenter.prevent>
                @if(isset($equipment[$slot]))
                    <div class="bg-green-300 p-2"
                         draggable="true"
                         @dragstart="$event.dataTransfer.setData('slot', '{{ $slot }}')">
                        {{ $equipment[$slot]->item->name }}
                    </div>
                @else
                    <span class="text-gray-400">Порожньо ({{ $slot }})</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
