<div>
    <div>
        @if ($backLink)
            <a href="{{ $backLink }}" class="mb-3 block md:-ml-4 hover:text-py-blue">
                <x-heroicon-s-chevron-left class="h-4 inline mb-1" />
                {{ $backText }}
            </a>
        @endif
        <div class="flex justify-between items-center">
            @if (isset($headerTitle))
                <h1 class="font-bold text-3xl text-gray-600">{{ $headerTitle }}</h1>
            @endif

            @if (isset($rightButtons) && !empty($rightButtons))
                @foreach ($rightButtons as $button)
                    <a href="{{ $button['url'] }}" class="btn bg-py-blue text-white text-center">
                        @if (isset($button['icon']))
                            {!! $button['icon'] !!}
                        @endif
                        {{ $button['text'] }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>



</div>
