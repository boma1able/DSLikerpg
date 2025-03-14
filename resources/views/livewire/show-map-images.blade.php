<div class="w-full min-w-[300px] h-[250px] relative">
    <img src="{{ $this->getImagePath() }}" class="object-cover" style="width: 100%; height: 100%; display: block;" alt="landscape">
    <div class="absolute w-full bg-[#1b1b18c2] bottom-1.5 z-1 text-gray-300 text-[.6rem] p-2 text-center">
        {{ $this->getStepExcerpt() }}
    </div>
</div>
