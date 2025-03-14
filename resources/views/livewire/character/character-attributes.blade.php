<div class="w-140 relative">
    <h2 class="text-xl text-gray-500 font-bold mb-4">Атрибути персонажа</h2>

    <div class="flex mb-4">
        <label for="body" class="block">Тіло:</label>
        <p class="mx-2text-center">{{ $body }}</p>
    </div>

    <div class="flex mb-4">
        <label for="strength" class="block">Сила:</label>
        <p class="mx-2 text-center">{{ $strength }}</p>
    </div>

    <div class="flex mb-4">
        <label for="agility" class="block">Ловкість:</label>
        <p class="mx-2 text-center">{{ $agility }}</p>
    </div>

    <div class="flex mb-4">
        <label for="intelligence" class="block">Розум:</label>
        <p class="mx-2 text-center">{{ $intelligence }}</p>
    </div>

    <div class="mt-6">
        <h3 class="font-bold">Бонуси:</h3>
        <ul>
            <li>Здоров'я: {{ $this->calculateHealthFromBody() }} HP</li>
            <li>Мана: {{ $this->calculateManaFromIntelligence() }} мана</li>
            <li>Шкода: {{ $this->calculateDamageFromStrength() }} шкоди</li>
            <li>Броня: {{ $this->calculateArmorFromAgility() }} броні</li>
        </ul>
    </div>

    <button wire:click="closeModal" class="absolute top-2 right-2 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>
