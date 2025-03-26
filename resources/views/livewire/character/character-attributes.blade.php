<div class="relative">
    <h2 class="text-xl text-gray-500 font-bold mb-4">Атрибути персонажа</h2>

    <div wire:poll.2s="loadBuffs">
        @foreach ($buffs as $buff)
            @if ($buff->is_active)
                <li class="flex justify-between whitespace-nowrap">
                    <span class="mr-2">[{{ $buff->buff_amount }}] {{ $buff->label }}</span>
                </li>
            @endif
        @endforeach
    </div>


    <div class="overflow-x-auto">
        <table class="min-w-full text-xs bg-white border border-gray-300">
            <thead>
            <tr class="bg-gray-200">
                <th class="px-2 py-2 border border-gray-300"></th>
                <th class="px-2 py-2 border border-gray-300">Базові</th>
                <th class="px-2 py-2 border border-gray-300">Гільдії</th>
                <th class="px-2 py-2 border border-gray-300">Спорядження</th>
                <th class="px-2 py-2 border border-gray-300">Бафи</th>
                <th class="px-2 py-2 border border-gray-300">Разом</th>
            </tr>
            </thead>
            <tbody>
                <!-- Рядки таблиці -->
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Тіло</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_body'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_body_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_buff_body_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['body'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Сила</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_strength'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_strength_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['strength'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Спритність</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_dexterity'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_dexterity_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['dexterity'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Інтелект</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_intelligence'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_intelligence_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['intelligence'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Урон</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_damage'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_damage_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['damage'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Шанс удару</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_hit_chance'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_hit_chance_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['hit_chance'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Захист</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Магічний урон</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_magic_damage'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_magic_damage_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['magic_damage'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Шанс магічного удару</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_magic_hit_chance'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_magic_hit_chance_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['magic_hit_chance'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Броня</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_armor'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_armor_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['armor'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Здоровʼя</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_health'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_health_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_buff_health_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['max_health'] }}</td>
                </tr>
                <tr>
                    <td class="px-2 py-2 border border-gray-300 whitespace-nowrap">Мана</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['base_mana'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['school_mana_bonus'] }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">-</td>
                    <td class="px-2 py-2 border border-gray-300 text-center">{{ $this->character['max_mana'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-gray-300 mt-2 p-1 text-xs">
        Скілпоінти: {{ $character->skill_points }}
    </div>


    <button wire:click="closeModal" class="absolute top-2 right-2 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>
