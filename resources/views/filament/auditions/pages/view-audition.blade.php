<x-filament-panels::page>
    <div class="bg-white rounded-md px-12 py-10">
        <div class="flex flex-row justify-between items-start ">
            <div class="w-[60%]">
                <div class="mb-4">
                    <h1 class="font-bold text-md mb-2">Created by: <span
                            class="font-normal">{{ $this->audition->project->user->email }}</span></h1>
                    <h1 class="font-bold text-md mb-2">
                        Countries:
                        <span class="font-normal">
                            @foreach ($this->audition->countries as $k => $country)
                                {{ $country->name }} {{ $k == count($this->audition->countries) - 1 ? '' : ',' }}
                            @endforeach
                        </span>
                    </h1>

                    <h1 class="font-bold text-md mb-2">Project: <span
                            class="font-normal">{{ $this->audition->project->title }}</span></h1>
                </div>

                <h1 class="font-bold text-xl mb-2">{{ $this->audition->title }}</h1>
                <p class="text-gray-800 text-base">{{ $this->audition->description }}</p>
            </div>
            <div class="w-[40%] pr-4">
                <img src="{{  config('aws.aws-urls.admin_base_url') . $this->audition->image }}" class="w-full block" />
                <div class="my-4">
                    @foreach ($this->tags as $tag)
                        <x-auditions.tag :text="$tag" />
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mt-4 py-10 border-y border-y-1 border-y-gray-400">

            <x-auditions.info-block label="Date" :value="$this->audition->filming_date">
                <x-icons.audition class="h-5 w-5" />
            </x-auditions.info-block>

            <x-auditions.info-block label="Deadline" :value="$this->audition->applications_deadline">
                <x-icons.time class="h-5 w-5" />
            </x-auditions.info-block>

            <x-auditions.info-block label="Location" :value="$this->audition->location">
                <x-icons.pin class="h-5 w-5" />
            </x-auditions.info-block>

            <x-auditions.info-block label="Duration" :value="$this->audition->duration">
                <x-icons.clock class="h-5 w-5" />
            </x-auditions.info-block>

            <x-auditions.info-block label="Budget" :value="$this->audition->total_budget . ' ' . $this->audition->currency">
                <x-icons.dollar class="h-5 w-5" />
            </x-auditions.info-block>
        </div>

        <div class="py-6">
            <h2 class="font-bold text-black">Directors</h2>
            <div class="py-2 ">
                <p class="inline mr-4 text-gray-800">Nader Jaffal</p>
                <p class="inline mr-4 text-gray-800">Nader Jaffal</p>
                <p class="inline mr-4 text-gray-800">Nader Jaffal</p>
            </div>
        </div>

        <div class="flex flex-row justify-end">
            <a href="{{ $this->navigationUrls['back'] }}" class="btn border-py-orange text-py-orange">Cancel</a>
            <a href="{{ $this->navigationUrls['edit'] }}" class="btn border-py-blue bg-py-blue text-white ml-4">Edit</a>
        </div>
    </div>
</x-filament-panels::page>
