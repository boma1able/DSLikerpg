<div class="border p-4">
    <h2 class="text-lg font-bold">🎒 Інвентар</h2>
    <div class="grid grid-cols-4 gap-2">
        @foreach($inventoryItems as $inventory)
            <div class="border p-2 text-center bg-gray-200"
                 draggable="true"
                 @dragstart="$event.dataTransfer.setData('inventoryId', '{{ $inventory->id }}')">
                {{ $inventory->item->name }}
            </div>
        @endforeach
    </div>
</div>
