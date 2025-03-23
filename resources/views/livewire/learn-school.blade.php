<div class="flex gap-4">
    <!-- Список шкіл -->
    <div class="w-1/4">
        <ul>
            <li class="cursor-pointer" wire:click="$set('selectedSchool', 'body')">Школа Тіла</li>
            <li class="cursor-pointer" wire:click="$set('selectedSchool', 'strength')">Школа Сили</li>
        </ul>
    </div>

    <!-- Бонуси для обраної школи -->
    <div class="w-100 p-4 bg-gray-100 rounded-lg">
        <h2 class="text-lg font-bold">Школа {{ ucfirst($selectedSchool) }}</h2>

        @php
            $currentSchool = $schools->where('school_name', $selectedSchool)->first();
            $bonuses = $schoolLevels[$selectedSchool] ?? [];
            $currentLevel = $currentSchool->level ?? 0;
            $nextAvailableLevel = $currentLevel + 1;
        @endphp

        <p>Рівень школи: {{ $currentLevel }}/15</p>
        <p>Скілпоінти: {{ $character->skill_points }}</p>

        <div class="flex flex-wrap gap-1 mt-2 font-semibold">
            @foreach ($bonuses as $level => $bonus)
                @php
                    $bodyBonus = $bonus['body'] ?? 0;
                    $healthBonus = $bonus['max_health'] ?? 0;
                    $strengthBonus = $bonus['strength'] ?? 0;
                    $damageBonus = $bonus['damage'] ?? 0;
                @endphp

                @if ($level <= $currentLevel)
                    <button class="w-full text-start text-xs text-[#0ac816]">
                        <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                        @if ($bodyBonus > 0)<span class="mr-2">+ {{ $bodyBonus }} Тіло</span>@endif
                        @if ($strengthBonus > 0)<span class="mr-2">+ {{ $strengthBonus }} Сила</span>@endif
                        @if ($damageBonus > 0)<span class="mr-2">+ {{ $damageBonus }} Урон</span>@endif
                        @if ($healthBonus > 0)<span class="mr-2">+ {{ $healthBonus }} здоровʼя</span>@endif
                    </button>
                @elseif ($level === $nextAvailableLevel && $character->skill_points > 0)
                    <button wire:click="learnLevel({{ $level }})" class="w-full text-start text-xs text-blue-700 cursor-pointer">
                        <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                        @if ($bodyBonus > 0)<span class="mr-2">+ {{ $bodyBonus }} Тіло</span>@endif
                        @if ($strengthBonus > 0)<span class="mr-2">+ {{ $strengthBonus }} Сила</span>@endif
                        @if ($damageBonus > 0)<span class="mr-2">+ {{ $damageBonus }} Урон</span>@endif
                        @if ($healthBonus > 0)<span class="mr-2">+ {{ $healthBonus }} здоровʼя</span>@endif
                    </button>
                @else
                    <button class="w-full text-start text-xs text-[#c82d0a] cursor-not-allowed">
                        <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                        @if ($bodyBonus > 0)<span class="mr-2">+ {{ $bodyBonus }} Тіло</span>@endif
                        @if ($strengthBonus > 0)<span class="mr-2">+ {{ $strengthBonus }} Сила</span>@endif
                        @if ($damageBonus > 0)<span class="mr-2">+ {{ $damageBonus }} Урон</span>@endif
                        @if ($healthBonus > 0)<span class="mr-2">+ {{ $healthBonus }} здоровʼя</span>@endif
                    </button>
                @endif
            @endforeach
        </div>
    </div>

</div>
