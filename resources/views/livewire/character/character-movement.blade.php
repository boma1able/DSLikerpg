<div style="display: inline-block; text-align: center; padding: 15px 0;">
    <div class="block pb-4">
        <button wire:click="move('up')" wire:loading.attr="disabled" wire:keydown.window="moveByKey($event.key)" class="p-3 text-white border bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-5 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
        </button>
    </div>
    <div class="block">
        <button wire:click="move('left')" wire:loading.attr="disabled" wire:keydown.window="moveByKey($event.key)" class="p-3 text-white border" style="transform: rotate(-90deg);background-color: #eee">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-5 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
        </button>
        <button wire:click="startResting" wire:loading.attr="disabled" wire:keydown.window="resting($event.key)" class="p-3 text-white border" style="margin: 0 10px;background-color: #eee">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"><g stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M6.5 11C7.06692 11.6303 7.75638 12 8.5 12C9.24362 12 9.93308 11.6303 10.5 11" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <path d="M13.5 11C14.0669 11.6303 14.7564 12 15.5 12C16.2436 12 16.9331 11.6303 17.5 11" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> <path d="M13 16C13 16.5523 12.5523 17 12 17C11.4477 17 11 16.5523 11 16C11 15.4477 11.4477 15 12 15C12.5523 15 13 15.4477 13 16Z" fill="#1C274C"></path> <path d="M17 4L20.4641 2L19 7.4641L22.4641 5.4641" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M14.0479 5.5L15.7799 6.5L13.0479 7.23205L14.7799 8.23205" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M22 12C22 17.5228 17.5228 22 12 22C10.1786 22 8.47087 21.513 7 20.6622M12 2C6.47715 2 2 6.47715 2 12C2 13.8214 2.48697 15.5291 3.33782 17" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
        </button>
        <button wire:click="move('right')" wire:loading.attr="disabled" wire:keydown.window="moveByKey($event.key)" class="p-3 text-white border" style="transform: rotate(90deg);background-color: #eee">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-5 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
        </button>
    </div>
    <div class="block" style="margin-top: 10px;">
        <button wire:click="move('down')" wire:loading.attr="disabled" wire:keydown.window="moveByKey($event.key)" class="p-3 bg-blue-500 text-white border" style="transform: rotate(180deg);background-color: #eee">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-5 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
        </button>
    </div>
</div>

<script>
    document.addEventListener('keydown', (event) => {
        Livewire.dispatch('moveByKey', event.key);
        if (event.key.toLowerCase() === 'z') {
            Livewire.dispatch('resting', [event.key]);
        }
    });
</script>
