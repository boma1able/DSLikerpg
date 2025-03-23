<div class="flex gap-4">
    <!-- Список шкіл -->
    <div class="">
        <ul>
            <li class="cursor-pointer whitespace-nowrap" wire:click="$set('selectedSchool', 'body')">Школа Тіла</li>
            <li class="cursor-pointer whitespace-nowrap" wire:click="$set('selectedSchool', 'strength')">Школа Сили</li>
            <li class="cursor-pointer whitespace-nowrap" wire:click="$set('selectedSchool', 'dexterity')">Школа Спритності</li>
            <li class="cursor-pointer whitespace-nowrap" wire:click="$set('selectedSchool', 'intelligence')">Школа Інтелекту</li>
        </ul>
    </div>

    <!-- Бонуси для обраної школи -->
    <div class="w-full p-4 bg-gray-100 rounded-lg">
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
                    $dexterityBonus = $bonus['dexterity'] ?? 0;
                    $armorBonus = $bonus['armor'] ?? 0;
                    $intelligenceBonus = $bonus['intelligence'] ?? 0;
                    $magicDamageBonus = $bonus['magic_damage'] ?? 0;
                @endphp

                @if ($level <= $currentLevel)
                    <button class="w-60 text-start text-xs text-[#0ac816]">
                        <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                        @if ($bodyBonus > 0)<span class="mr-2">+ {{ $bodyBonus }} Тіло</span>@endif
                        @if ($strengthBonus > 0)<span class="mr-2">+ {{ $strengthBonus }} Сила</span>@endif
                        @if ($damageBonus > 0)<span class="mr-2">+ {{ $damageBonus }} Урон</span>@endif
                        @if ($healthBonus > 0)<span class="mr-2">+ {{ $healthBonus }} здоровʼя</span>@endif
                        @if ($dexterityBonus > 0)<span class="mr-2">+ {{ $dexterityBonus }} Спритність</span>@endif
                        @if ($intelligenceBonus > 0)<span class="mr-2">+ {{ $intelligenceBonus }} Інтелект</span>@endif
                        @if ($armorBonus > 0)<span class="mr-2">+ {{ $armorBonus }} Броні</span>@endif
                        @if ($magicDamageBonus > 0)<span class="mr-2">+ {{ $magicDamageBonus }} Магічний урон</span>@endif
                    </button>
                @elseif ($level === $nextAvailableLevel && $character->skill_points > 0)
                    <button wire:click="learnLevel({{ $level }})" class="w-60 text-start text-xs text-blue-700 cursor-pointer">
                        <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                        @if ($bodyBonus > 0)<span class="mr-2">+ {{ $bodyBonus }} Тіло</span>@endif
                        @if ($strengthBonus > 0)<span class="mr-2">+ {{ $strengthBonus }} Сила</span>@endif
                        @if ($damageBonus > 0)<span class="mr-2">+ {{ $damageBonus }} Урон</span>@endif
                        @if ($healthBonus > 0)<span class="mr-2">+ {{ $healthBonus }} здоровʼя</span>@endif
                        @if ($dexterityBonus > 0)<span class="mr-2">+ {{ $dexterityBonus }} Спритність</span>@endif
                        @if ($intelligenceBonus > 0)<span class="mr-2">+ {{ $intelligenceBonus }} Інтелект</span>@endif
                        @if ($armorBonus > 0)<span class="mr-2">+ {{ $armorBonus }} Броні</span>@endif
                        @if ($magicDamageBonus > 0)<span class="mr-2">+ {{ $magicDamageBonus }} Магічний урон</span>@endif
                    </button>
                @else
                    <button class="w-60 text-start text-xs text-[#c82d0a] cursor-not-allowed">
                        <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                        @if ($bodyBonus > 0)<span class="mr-2">+ {{ $bodyBonus }} Тіло</span>@endif
                        @if ($strengthBonus > 0)<span class="mr-2">+ {{ $strengthBonus }} Сила</span>@endif
                        @if ($damageBonus > 0)<span class="mr-2">+ {{ $damageBonus }} Урон</span>@endif
                        @if ($healthBonus > 0)<span class="mr-2">+ {{ $healthBonus }} здоровʼя</span>@endif
                        @if ($dexterityBonus > 0)<span class="mr-2">+ {{ $dexterityBonus }} Спритність</span>@endif
                        @if ($intelligenceBonus > 0)<span class="mr-2">+ {{ $intelligenceBonus }} Інтелект</span>@endif
                        @if ($armorBonus > 0)<span class="mr-2">+ {{ $armorBonus }} Броні</span>@endif
                        @if ($magicDamageBonus > 0)<span class="mr-2">+ {{ $magicDamageBonus }} Магічний урон</span>@endif
                    </button>
                @endif
            @endforeach
        </div>
    </div>

</div>
