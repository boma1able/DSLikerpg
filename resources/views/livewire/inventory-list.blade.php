<div>
    <h2 class="text-lg font-bold mb-3">Інвентар</h2>

    @if($inventory->isEmpty())
        <p>Ваш інвентар порожній.</p>
    @else
        <div class="w-104 grid grid-cols-5 grid-rows-5 gap-1 border-2 border-gray-800">
            @foreach ($inventory as $slot)
                <div class="relative w-20 h-20 group">
                    <img src="{{ asset( $slot->item->image ?? 'no-image.jpg') }}"
                         class="w-full h-full"
                         style="filter: brightness(1.3);"
                         alt="">
                    <div class="absolute grid flex-nowrap w-40 top-1/2 left-1/2 p-2 bg-white z-1 border border-gray-300 rounded-xs text-xs opacity-0 group-hover:opacity-100">
                        <span class="text-gray-400 text-center">{{ ucfirst($slot->item->rarity) }}</span><br>
                        <span class="text-center">{{ $slot->item->name }}</span>
                        <span>Рівень: {{ $slot->item->level }}</span>
                        @if($slot->item->type === 'weapon')
                            <span>Шкода: 0</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
