<div>
    @if (isset($backLink))
        <a href="{{ $backLink }}" class="mb-3 block -ml-4 hover:text-py-blue">
            <x-heroicon-s-chevron-left class="h-4 inline mb-1" />
            {{ $backText }}
        </a>
    @endif
    <div class="flex flex-col md:flex-row justify-between md:items-center">
        <div class="mb-4 md:mb-0">
            @if ($title)
                <h1 class="text-2xl font-medium text-py-blue">{{ $title }}</h1>
            @endif
            @if ($subtitle)
                <p class="text-base text-black font-medium">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="flex flex-col md:flex-row md:w-[60%]">
            @if ($placeholder)
                <input type="text"
                    class="flex-1 mr-4 rounded-md border border-white placeholder:text-gray-700 mb-4 md:mb-0"
                    wire:model.live.debounce.300ms="searchQuery" placeholder="{{ $placeholder }}" />
            @endif
            @if ($buttonText)
                @if (!auth()->user()->hasRole('director'))
                    <a href="{{ $buttonLink }}" class="btn bg-py-blue text-white text-center">
                        <x-filament::icon icon="heroicon-s-plus"
                            class="h-5 w-5 text-gray-500 dark:text-gray-400 inline text-white block" />
                        {{ $buttonText }} </a>
                @endif
            @endif
        </div>
    </div>
</div>
