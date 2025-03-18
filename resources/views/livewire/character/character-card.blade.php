<div class="w-[83px] border-r-[3px] border-[#eee]">
    <div class="group relative"
         x-data="{ x: 0, y: 0 }"
         @mousemove="x = $event.offsetX; y = $event.offsetY"
    >
        <div class="w-[80px] h-[110px]">
            <img src="{{ asset('storage/' . (auth()->user()->character->avatar ?? 'default-avatar.jpg')) }}"
                 style="display: block;"
                 class="w-full h-full object-cover"
                 alt="Character Avatar">
        </div>
        <div>
            <div class="relative flex bg-[#6767677d] text-[8px] text-black">
                <span style="position: relative; z-index: 1; height: 8px; text-align: center; width: 100%; line-height: 12px; font-weight: 600">{{ $character->health }} / {{ $character->max_health }}</span>
                <span style="position: absolute; top: 0; left: 0; width: {{ $character->max_health > 0 ? ($character->health / $character->max_health) * 100 : 0 }}%; background: linear-gradient(90deg, #fc6363 0%, #da2d2d 70%, #8b0000 90%); height: 100%;"></span>
            </div>
            <div class="relative flex bg-[#6767677d] text-[8px] text-black">
                <span style="position: relative; z-index: 1; height: 8px; text-align: center; width: 100%; line-height: 12px; font-weight: 600">{{ $character->mana }} / {{ $character->max_mana }}</span>
                <span style="position: absolute; top: 0; left: 0; width: {{ $character->max_mana > 0 ? ($character->mana / $character->max_mana) * 100 : 0 }}%; background: linear-gradient(90deg, #63adfc 0%, #322dda 70%, #02356b 90%); height: 100%;"></span>
            </div>
            <div class="relative flex bg-[#6767677d] text-[8px] text-black">
                <span style="position: relative; z-index: 1; height: 8px; text-align: center; width: 100%; line-height: 12px; font-weight: 600">{{ $experience }} / {{ $requiredExperience }}</span>
                <span style="position: absolute; top: 0; left: 0; width: {{ ($experience / $requiredExperience) * 100 }}%;
                    background: linear-gradient(90deg, #9afc63 0%, #43da2d 70%, #10a004 90%); height: 100%;"></span>
            </div>
        </div>

        <!-- Tooltip text following the mouse -->
        <div class="absolute w-auto left-0 top-0 border border-gray-400 bg-white p-2 text-xs z-[-1] opacity-0 group-hover:opacity-100 group-hover:z-1"
             :style="'left: ' + (x + 14) + 'px; top: ' + (y + 14) + 'px'"
        >
            <span style="white-space: nowrap">{{ auth()->user()->name }} [{{ $character->level }}]</span>
        </div>

        @if ($isResting)
            <div class="absolute top-[5px] left-[5px]">
                <svg class="w-4 h-4" viewBox="0 0 48 48" stroke="#00f3ff" stroke-width="4" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <circle cx="229.5851" cy="25.1368" r="5.6345" />
                        <path style="fill: transparent;" d="M35.22,25.1368c0-8.5012-5.239-11.17-9.885-11.17s-11.3678,2.3724-11.3678,11.17,4.4482,14.8276,15.6184,14.8276c10.577,0,15.9149-8.3035,15.9149-14.8276C45.5,18.9092,44.5774,2.5,24.1812,2.5,6.5529,2.5,2.5,17.9207,2.5,25.1368A30.0767,30.0767,0,0,0,10.0126,43.523" />
                    </g>
                </svg>
            </div>
        @endif
    </div>
</div>
