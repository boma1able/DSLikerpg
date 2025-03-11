<div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg">
    <form wire:submit="login" class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" wire:model.defer="email"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 px-3 py-2">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
            <input type="password" id="password" wire:model.defer="password"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 px-3 py-2">
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Увійти
        </button>
    </form>
    <div class="mt-4">
        Don't have an account? <a href="/register" wire:navigate>Register</a>
    </div>
</div>
