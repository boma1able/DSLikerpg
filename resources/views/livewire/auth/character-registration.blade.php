<div class="container w-100 m-auto h-full">
    <form wire:submit.prevent="registerCharacter" >
        <h2>Створити персонажа</h2>

        @if ($step === 1)
            <div>
                <div class="mb-4">
                    <label for="nickname">Нік персонажа</label>
                    <input type="text" id="nickname" wire:model="nickname" required class="w-full border p-2">
                </div>

                <div class="mb-4">
                    <label for="email">Email</label>
                    <input type="email" id="email" wire:model="email" required class="w-full border p-2">
                </div>

                <div class="mb-4">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" wire:model="password" required class="w-full border p-2">
                </div>
            </div>
        @endif

        @if ($step === 2)
            <div>
                <label>Раса</label>
                <div class="flex items-center gap-4">
                    @foreach (['Human' => 'human.webp', 'Elf' => 'elf.jpg', 'Orc' => 'orc.jpg'] as $value => $image)
                        <label
                            class="mr-4 "
                            style="width: 100px; height: 150px;"
                            wire:click="$set('race', '{{ $value }}')"
                        >
                            <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover border-2 {{ $race === $value ? 'border-blue-500' : 'border-transparent' }}">
                        </label>
                    @endforeach

                </div>
            </div>
        @endif

        @if ($step === 3)
            <div>
                <label>Аватар</label>
                <div class="flex items-center gap-4">
                    @foreach ($this->getAvatarsForRace() as $avatar)
                        <div style="width: 100px; height: 150px;">
                            <img src="{{ asset('storage/' . $avatar) }}" wire:click="$set('avatar', '{{ $avatar }}')"
                                 class="w-24 h-36 cursor-pointer border-2 {{ $avatar === $this->avatar ? 'border-blue-500' : 'border-transparent' }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($step === 4)
            <div>
                <label>Клас</label>
                <select wire:model="class" required class="border p-2">
                    <option >Choose your role</option>
                    <option value="Tank">Tank</option>
                    <option value="Warrior">Warrior</option>
                    <option value="Assassin">Assassin</option>
                    <option value="Mage">Mage</option>
                </select>
            </div>
        @endif

        <div class="flex justify-between mt-4">
            @if ($step > 1)
                <button type="button" wire:click="prevStep" class="px-4 py-2 bg-gray-300">Назад</button>
            @endif

            @if ($step < 4)
                <button type="button" wire:click="nextStep" class="px-4 py-2 bg-blue-500 text-white">Далі</button>
            @else
                <button type="submit" class="px-4 py-2 bg-green-500 text-white">Створити персонажа</button>
            @endif
        </div>
    </form>

    @if (session()->has('message'))
        <div class="alert alert-success mt-4">
            {{ session('message') }}
        </div>
    @endif
</div>

