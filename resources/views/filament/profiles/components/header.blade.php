<div>
    @if (isset($backLink))
        <a href="{{ $backLink }}" class="mb-3 block -ml-4 hover:text-py-blue">
            <x-heroicon-s-chevron-left class="h-4 inline mb-1" />
            {{ $backText }}
        </a>
    @endif
    <div class="flex flex-row justify-between items-center mb-2">
        <div>
            <h1 class="text-2xl font-medium text-py-blue">{{ $title }} <span
                    class="text-lg text-py-orange">#{{ $applicant_id }}</span></h1>
            <p class="text-base text-black font-medium">{{ $subtitle }} </p>
        </div>
        <div class="flex flex-row w-[5x%]">
            @if(isset($previousCandidateUrl))
            <a href="{{$previousCandidateUrl}}" class="btn bg-py-blue text-white flex flex-row items-center">
                <x-icons.arrow-left />
                <span class="ml-2">Previous Candidate</span>
            </a>
            @endif

            @if(isset($nextCandidateUrl))
            <a href="{{$nextCandidateUrl}}" class="btn bg-py-blue text-white flex flex-row items-center">
                <span class="mr-2">Next Candidate</span>
                <x-icons.arrow-right />
            </a>
            @endif

        </div>
    </div>
</div>
