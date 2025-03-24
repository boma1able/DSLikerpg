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
        <h2 class="text-lg font-bold">Школа {{ ucfirst($this->selectedSchoolLabel) }}</h2>

        @php
            $currentSchool = $schools->where('school_name', $selectedSchool)->first();
            $bonuses = $schoolLevels[$selectedSchool] ?? [];
            $currentLevel = $currentSchool->level ?? 0;
            $nextAvailableLevel = $currentLevel + 1;
        @endphp

        <p>Рівень школи: {{ $currentLevel }}/15</p>
        <p>Скілпоінти: {{ $character->skill_points }}</p>

        @php
            $attributeLabels = [
                'body' => 'Тіло',
                'max_health' => 'здоровʼя',
                'strength' => 'Сила',
                'damage' => 'Урон',
                'dexterity' => 'Спритність',
                'armor' => 'Броня',
                'intelligence' => 'Інтелект',
                'magic_damage' => 'Магічний урон',
                'max_mana' => 'Мана',
                'hit_chance' => 'Шанс удару',
                'magic_hit_chance' => 'Шанс магічного удару',
            ];
        @endphp

        <div class="flex flex-wrap gap-1 mt-2 font-semibold">
            @foreach ($bonuses as $level => $bonus)
                @if ($level <= $currentLevel)
                    <button class="w-80 text-start text-xs text-gray-500">
                @elseif ($level === $nextAvailableLevel && $character->skill_points > 0)
                    <button wire:click="learnLevel({{ $level }})" class="w-80 text-start text-xs text-blue-700 cursor-pointer">
                @else
                    <button class="w-80 text-start text-xs text-[#a42205] cursor-not-allowed">
                @endif
                    <span class="inline-block w-3 text-center mr-3">{{ $level }}</span>
                @foreach ($bonus as $attribute => $value)
                    @if ($value > 0)
                        <span class="mr-2">+ {{ $value }} {{ $attributeLabels[$attribute] ?? $attribute }}</span>
                    @endif
                @endforeach
                </button>
            @endforeach
        </div>


    </div>

</div>
