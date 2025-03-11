<div class="p-4 bg-white rounded shadow-md">
    <h2 class="text-xl text-gray-500 font-bold mb-4">Атрибути персонажа</h2>

    <div class="mb-4">
        <label for="body" class="block">Тіло:</label>
        <div class="flex items-center">
            <button wire:click="increment('body')" class="bg-green-500 text-white px-2 py-1 rounded">+</button>
            <input type="number" id="body" wire:model="body" class="mx-2 p-2 border border-gray-300 rounded w-16 text-center" readonly>
            <button wire:click="decrement('body')" class="bg-red-500 text-white px-2 py-1 rounded">-</button>
        </div>
    </div>

    <div class="mb-4">
        <label for="strength" class="block">Сила:</label>
        <div class="flex items-center">
            <button wire:click="increment('strength')" class="bg-green-500 text-white px-2 py-1 rounded">+</button>
            <input type="number" id="strength" wire:model="strength" class="mx-2 p-2 border border-gray-300 rounded w-16 text-center" readonly>
            <button wire:click="decrement('strength')" class="bg-red-500 text-white px-2 py-1 rounded">-</button>
        </div>
    </div>

    <div class="mb-4">
        <label for="agility" class="block">Ловкість:</label>
        <div class="flex items-center">
            <button wire:click="increment('agility')" class="bg-green-500 text-white px-2 py-1 rounded">+</button>
            <input type="number" id="agility" wire:model="agility" class="mx-2 p-2 border border-gray-300 rounded w-16 text-center" readonly>
            <button wire:click="decrement('agility')" class="bg-red-500 text-white px-2 py-1 rounded">-</button>
        </div>
    </div>

    <div class="mb-4">
        <label for="intelligence" class="block">Розум:</label>
        <div class="flex items-center">
            <button wire:click="increment('intelligence')" class="bg-green-500 text-white px-2 py-1 rounded">+</button>
            <input type="number" id="intelligence" wire:model="intelligence" class="mx-2 p-2 border border-gray-300 rounded w-16 text-center" readonly>
            <button wire:click="decrement('intelligence')" class="bg-red-500 text-white px-2 py-1 rounded">-</button>
        </div>
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
</div>
