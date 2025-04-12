<div class="p-4 bg-gray-100 rounded-xl space-y-4">
    <h2 class="text-lg font-bold">🏦 Банк</h2>

    <div>
        <p>💰 У вас: <strong>{{ $gold }}</strong> золота</p>
        <p>🏦 У банку: <strong>{{ $bankGold }}</strong> золота</p>
    </div>

    <div class="flex items-center space-x-2">
        <input type="number" wire:model="amount" min="1"
               class="border px-2 py-1 rounded w-24"
               placeholder="Сума">
        <button wire:click="deposit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
            Покласти
        </button>
        <button wire:click="withdraw" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
            Зняти
        </button>
    </div>
    @if (session()->has('message'))
        <div class="text-sm text-green-600 mb-2">
            {{ session('message') }}
        </div>
    @endif
    @error('amount')
    <div class="text-sm text-red-600 mb-2">
        {{ $message }}
    </div>
    @enderror
</div>
