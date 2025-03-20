<div class="p-4 bg-gray-100 rounded-lg">
    <h2 class="text-lg font-bold">Школа Тіла</h2>
    <p>Рівень школи: {{ $school->level }}/15</p>
    <p>Скілпоінти: {{ $character->skill_points }}</p>

    <div class="flex flex-wrap gap-1 mt-2">
        @for ($i = 1; $i <= 15; $i++)
            @if ($i <= $school->level)
                <button class="w-full bg-green-500 text-white text-xs p-2 rounded">✔️ Вивчено ({{ $i }})</button>
            @elseif ($i == $school->level + 1 && $character->skill_points > 0)
                <button wire:click="learnLevel" class="w-full text-xs bg-blue-500 text-white p-2 rounded">
                    Вивчити {{ $i }}
                </button>
            @else
                <button class="w-full bg-gray-300 text-xs text-gray-600 p-2 rounded cursor-not-allowed">
                    {{ $i }} 🔒
                </button>
            @endif
        @endfor
    </div>
</div>
