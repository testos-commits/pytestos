<div>
    @if (isset($backLink))
        <a href="{{ $backLink }}" class="mb-3 block -ml-4 hover:text-py-blue">
            <x-heroicon-s-chevron-left class="h-4 inline mb-1" />
            {{ $backText }}
        </a>
    @endif
    <div class="sm:flex sm:flex-row justify-between items-center">
        <div>
            <h1 class="text-2xl font-medium text-py-blue">{{ $title }}</h1>
            @if ($subtitle)
                <p class="text-base text-black font-medium">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="flex flex-row justify-between mt-2 sm:mt-0 sm:justify-end sm:w-[60%]">
            <a href="{{ route('filament.admin.resources.messages.list-audition-messages', ['record' => $this->audition->id]) }}"
                class="btn bg-py-blue text-white">
                <x-monoicon-filter class="h-5 text-white mr-2 inline" />
                Filter by answer </a>

            <a href="{{ $buttonLink }}" class="btn bg-py-blue text-white">
                <x-filament::icon icon="heroicon-s-plus" class="h-5 w-5 text-gray-500 inline text-white" />
                {{ $buttonText }} </a>
        </div>

    </div>

</div>
