<div>
    @if(Auth::check())
    <div class="flex border-3 border-[#e3e3e0]" style="gap: 20px; background: url({{ asset('storage/bg.jpeg') }}) top left no-repeat;background-size: cover; padding-right: 20px;">
        <div class="flex">

            <livewire:character.character-card/>

            <div class="w-[83px] border-r-[3px] border-[#eee]">
                @foreach ($monsters as $monster)
                    @if (-$monster['position_x'] === $offsetX && -$monster['position_y'] === $offsetY)
                        <div wire:click="startBattle({{ $monster['id'] }})" class="group" style="position:relative;">
                            <div style="position: absolute; width: 100%; height: calc(100% + 3px); border: {{ $monsterAttacked ? '3px solid red' : 'none' }};"></div>
                            <img src="{{ asset('storage/monsters/' . $monster['avatar']) }}"
                                 class="block w-[80px] h-[110px] object-cover" alt="Character Image">
                            <div class="flex relative bg-[#6767677d] text-[8px] text-black">
                                <span class="relative z-1 w-full h-[8px] items-center" style="line-height: 12px; font-weight: 600"></span>
                                <span style="position: absolute; top: 0; left: 0; width: {{ ($monster['max_health'] > 0 && $monster['health'] > 0) ? ($monster['health'] / $monster['max_health']) * 100 : 0 }}%; background: linear-gradient(90deg, #fc6363 0%, #da2d2d 70%, #a00404 90%); height: 100%;"></span>
                            </div>
                            <div class="absolute flex text-xs bottom-[20%] left-1/2 transform -translate-x-1/2 py-[2px] px-[6px] bg-white text-black text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span style="white-space: nowrap">{{ $monster['name'] }} ({{ $monster['level'] }})</span>
                            </div>
                        </div>
                        @if($inBattle)
                            <div wire:poll.2s="fight({{ $monster['id'] }})"></div>
                        @endif
                    @endif
                @endforeach
            </div>

            @if ($isResting)
                <div wire:poll.2s="restoreHealth"></div>
            @endif

        </div>

        {{--Play Field--}}
        <div class="flex flex-wrap w-[650px]">

            <div>

                <div style="display: flex;">
                    <div style="width:350px;height: 300px;border: 3px solid #eee;">

                        <livewire:show-map-images />

                        <div class="flex relative text-[8px] mt-[-4px] text-black">
                            <span class="relative z-1 w-full h-[4px] items-center" style="line-height: 6px; font-weight: 600"></span>
                        </div>
                    </div>

                    <div style="width: 300px; height: 300px;position:relative; overflow:hidden; background: #01978f; border: 5px solid #eee;">
                        <div style="position:absolute; top:50%; left:50%; width: 30px; height: 30px;z-index: 1">
                            <div class="grid"
                                 style="transform: translate({{ $offsetX * 30 }}px, {{ $offsetY * 30 }}px);
                                     grid-template-columns: repeat(11,minmax(0,1fr));
                                     width: 300px; height: 300px;
                                     z-index: -1;
                                     position:absolute;
                                     font-size: 0;
                                     ">
                                @foreach ($map as $y => $row)
                                    @foreach ($row as $x => $tile)
                                        <div class="flex items-center justify-center
                                            {{ $tile === 2 ? 'open4' : '' }}

                                            {{ $tile === 3 ? 'open2-vertical' : '' }}
                                            {{ $tile === 5 ? 'open2-top-right' : '' }}
                                            {{ $tile === 6 ? 'open2-bottom-right' : '' }}
                                            {{ $tile === 7 ? 'open2-horizontal' : '' }}
                                            {{ $tile === 9 ? 'open2-bottom-left' : '' }}
                                            {{ $tile === 10 ? 'open2-top-left' : '' }}

                                            {{ $tile === 4 ? 'block3-top' : '' }}
                                            {{ $tile === 12 ? 'block3-bottom' : '' }}
                                            {{ $tile === 16 ? 'block3-left' : '' }}
                                            {{ $tile === 14 ? 'block3-right' : '' }}

                                            {{ $tile === 8 ? 'block1-top' : '' }}
                                            {{ $tile === 13 ? 'block1-bottom' : '' }}
                                            {{ $tile === 15 ? 'block1-left' : '' }}
                                            {{ $tile === 11 ? 'block1-right' : '' }}
                                            "
                                            style="width: 30px; height: 30px;
                                            {{ $tile === 'x' ? 'background: transparent' : '' }}
                                            {{ $x === $characterX && $y === $characterY ? '' : '' }}"
                                        >
                                            @if (-$x === $offsetX && -$y === $offsetY)
                                                <span
                                                    class="block text-xl rounded-full w-[24px] h-[24px] p-2 {{ $x }} {{ $y }}"
                                                    style="background: radial-gradient(circle, rgb(255, 255, 255) 30%, rgb(255, 255, 255, .5) 70%, rgba(255, 255, 255, 0) 100%);">
                                                </span>
                                            @elseif ($count = collect($monsters)->where('position_x', $x)->where('position_y', $y)->count())
                                                <span class="relative text-xl">👹 <span class="absolute left-0 top-0 bg-white text-[8px]">{{ $count > 1 ? 'x' . $count : '' }}</span></span>
                                            @else
                                                {{ $tile }}
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div style="padding: 15px 0 0; color: #eee; font-size: 12px; line-height: 20px;">
                    <ul>
                        <li class="flex items-center">
                            <span style="display:inline-block;width: 100px;">Gold:</span>
                            <svg height="200px" width="200px" version="1.1" class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 511.882 511.882" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <polygon style="fill:#F6BB42;" points="350.216,176.572 278.374,158.615 37.038,264.123 0,338.207 125.753,374.324 386.13,258.531 "></polygon> <polygon style="fill:#FFCE54;" points="350.216,176.572 107.756,284.345 125.753,374.324 386.13,258.531 "></polygon> <polygon style="fill:#E8AA3D;" points="107.756,284.345 37.038,264.123 0.015,338.207 125.753,374.324 "></polygon> <polygon style="fill:#F6BB42;" points="475.969,212.682 404.127,194.717 162.791,300.232 125.753,374.324 251.504,410.41 511.882,294.625 "></polygon> <polygon style="fill:#FFCE54;" points="475.969,212.682 233.508,320.431 251.504,410.41 511.882,294.625 "></polygon> <polygon style="fill:#E8AA3D;" points="233.508,320.431 162.791,300.232 125.753,374.324 251.504,410.41 "></polygon> <polygon style="fill:#F6BB42;" points="396.316,119.429 324.488,101.473 103.867,198.435 66.843,272.519 192.596,308.621 432.245,201.379 "></polygon> <polygon style="fill:#FFCE54;" points="396.316,119.429 174.6,218.641 192.596,308.621 432.245,201.379 "></polygon> <polygon style="fill:#E8AA3D;" points="174.6,218.641 103.867,198.435 66.843,272.519 192.596,308.621 "></polygon> </g></svg>
                            <livewire:gold-manager :characterId="$character['id']"/>
                        </li>
{{--                        <li><span style="display:inline-block;width: 100px;">Damage:</span>{{ $character['damage'] }}</li>--}}
                    </ul>
                </div>

                {{--Lootbox--}}
                <div class="w-full h-[80px] border-2 border-[#eee] mt-4 overflow-hidden">
                    <div class="block w-20 h-20" style="background: radial-gradient(circle, #292929, #787575)">

                    </div>
                </div>

            </div>

            <div>
                {{--Log/chat--}}
                <div id="log-container" class="w-[650px] h-[150px] mt-4 bg-gray-100 border border-gray-300 overflow-y-auto"
                     style="overflow: auto; flex-direction: column-reverse; display: flex;scroll-behavior: smooth; overflow-anchor: auto; padding: 10px;">
                    <div>
                        @foreach($log as $entry)
                            <p style="font-size:12px;">{!! $entry !!}</p>
                        @endforeach
                    </div>
                </div>

                <livewire:character.character-movement :map="$map" :monsters="$monsters" :log="$log"/>

                <button wire:click="logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
                    Вийти
                </button>
            </div>

        </div>

        <div>
            <livewire:character.character-attributes :character="$character" />
        </div>

    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('respawn-monster-js', (monsterArray) => {
                let monster = monsterArray[0]; // Беремо об'єкт з масиву
                // console.log("🔄 Подія JS для респауну монстра:", monster);
                Livewire.dispatch('respawnMonster', monster);
            });
        });
    </script>


        <div>
        <!-- Попап -->
        @if ($showReviveModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-1">
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <p class="text-lg font-semibold">💀 Ви загинули!</p>
                    <p class="mb-4">Бажаєте воскреснути ?</p>
                    <button wire:click="revive" class="bg-green-500 text-white px-4 py-2 rounded">
                        Воскреснути
                    </button>
                </div>
            </div>
        @endif
    </div>


    @else

        @livewire('auth.login-form')

    @endif

</div>
