<x-filament-panels::page>

    <div class="grid grid-cols-1 sm:grid-cols-[minmax(70%,_1fr)_1fr] gap-8 mb-8">
        <div class="owl-carousel owl-theme bg-black" wire:ignore>
            @foreach ($this->application->profile->castingImages as $k => $image)
                <div class="item">
                    <div class="h-[600px] w-full bg-no-repeat bg-center bg-contain"
                        style="background-image: url('{{ $image->image }}')"></div>
                </div>
            @endforeach
        </div>


        <div class="bg-white rounded-md w-full py-8 px-8 text-lg h-fit">
            <h2 class="font-bold mb-4">Actions</h2>

            @if (auth()->user()->hasPermissionTo('access talent messages'))
                <div class="flex flex-row justify-start items-center mb-4">
                    <a href="{{ route('filament.admin.resources.messages.create', ['auditionId' => $this->audition->id, 'recipientId' => $this->application->profile->id]) }}"
                        class="bg-py-blue-200 mr-4 rounded-full h-10 w-10 flex items-center justify-center">
                        <x-icons.chat class="fill-py-blue" />
                    </a>
                    <p class='text-base'>Send a message</p>
                </div>
            @endif

            @if ($this->application->in_selection)
                <div class="flex flex-row justify-start items-center mb-4">
                    <button class="bg-py-blue-200 mr-4 rounded-full h-10 w-10 flex items-center justify-center"
                        wire:click="changeStatus('unselect')">
                        <x-icons.delete-orange />
                    </button>
                    <p class='text-base'>Remove from selection list</p>
                </div>
            @endif

            @if (!$this->application->in_selection)
                <div class="flex flex-row justify-start items-center mb-4">
                    <button class="bg-py-blue-200 mr-4 rounded-full h-10 w-10 flex items-center justify-center"
                        wire:click="changeStatus('select')">
                        <x-icons.add />
                    </button>
                    <p class='text-base'>Add to selection list</p>
                </div>
            @endif

            @if (auth()->user()->hasPermissionTo('change application status') ||
                    auth()->user()->hasPermissionTo('shortlist applicant'))
                @if ($this->application->is_shortlisted)
                    <div class="flex flex-row justify-start items-center mb-4">
                        <button class="bg-py-blue-200 mr-4 rounded-full h-10 w-10 flex items-center justify-center"
                            wire:click="changeStatus('unshortlist')">
                            <x-icons.unfavorite-orange />
                        </button>
                        <p class='text-base'>Remove from shortlisted</p>
                    </div>
                @endif

                @if (!$this->application->is_shortlisted)
                    <div class="flex flex-row justify-start items-center mb-4">
                        <button class="bg-py-blue-200 mr-4 rounded-full h-10 w-10 flex items-center justify-center"
                            wire:click="changeStatus('shortlist')">
                            <x-icons.favorite-orange />
                        </button>
                        <p class='text-base'>Add to shortlisted</p>
                    </div>
                @endif
            @endif

            @if (auth()->user()->hasPermissionTo('change application status'))
                @if ($this->application->is_accepted)
                    <button class="btn w-full mt-4 bg-py-blue-200 text-py-blue"
                        wire:click="changeStatus('unaccept')">Unaccept</button>
                @else
                    <button class="btn w-full mt-4 bg-py-blue text-white"
                        wire:click="changeStatus('accept')">Accept</button>
                @endif
            @endif
        </div>

    </div>

    <div class="bg-white py-8 px-8 ">
        <div class="w-full  border-b-2 border-b-py-blue-200 flex flex-row overflow-x-scroll">
            @foreach ($this->tabs as $k => $tab)
                <button data-target="tab-{{ $k }}"
                    class="w-[300px] text-sm sm:text-base font-bold py-2 px-4 border-b-2 border-transparent hover:border-b-py-blue 
                    {{ $k == 'about' ? 'border-b-py-blue' : '' }}
                    focus:outline-none tab-button tab-{{ $k }}">{{ $tab }}</button>
            @endforeach
        </div>


        <div class="p-8 tab-pane " id="tab-about">
            <p class="font-bold mb-2">User Reference: <span
                    class="font-light">#{{ $this->application->profile->id }}</span>
            </p>

            @if (auth()->user()->hasPermissionTo('view talent full name'))
                <p class="font-bold mb-2">Name: <span
                        class="font-light">{{ $this->application->profile->full_name }}</span>
                </p>
            @endif

            @if (auth()->user()->hasPermissionTo('view talent first name') &&
                    !auth()->user()->hasPermissionTo('view talent full name'))
                <p class="font-bold mb-2">Name: <span
                        class="font-light">{{ $this->application->profile->first_name }}</span>
                </p>
            @endif

            <p class="font-bold mb-2">Age: <span class="font-light">{{ $this->application->profile->age }}
                    y/o</span>
            </p>
            @if (auth()->user()->hasPermissionTo('view talent description'))
                <div class="mb-4">
                    <p class="font-bold mb-2">Description: </p>
                    <span class="font-light">{{ $this->application->profile->description }}</span>
                </div>
            @endif
            <p class="font-bold mb-2">Country: <span
                    class="font-light">{{ $this->application->profile->country->name }}</span>
            </p>

            <p class="font-bold mb-2">Nationality: <span
                    class="font-light">{{ $this->application->profile->adminDisplayNationalities() }}</span>
            </p>
            <p class="font-bold mb-2">Passport: <span
                    class="font-light">{{ $this->application->profile->adminDisplayPassports() }}</span>
            </p>
            <p class="font-bold mb-2">Shoes Size: <span
                    class="font-light">{{ $this->application->profile->shoes }}</span>
            </p>

            <p class="font-bold mb-2">Shirt Size: <span
                    class="font-light">{{ $this->application->profile->shirt }}</span>

            <p class="font-bold mb-2">Pants Size: <span
                    class="font-light">{{ $this->application->profile->pants }}</span>
            </p>

            <p class="font-bold mb-2">Weight: <span class="font-light">{{ $this->application->profile->weight }}</span>
            </p>

            <p class="font-bold mb-2">Height: <span class="font-light">{{ $this->application->profile->height }}</span>
            </p>


            <p class="font-bold mb-2">Gender: <span class="font-light">{{ $this->application->profile->gender }}</span>
            </p>


            <p class="font-bold mb-2">University: <span
                    class="font-light">{{ $this->application->profile->university ?? 'Not Specified' }}</span>
            </p>


            <p class="font-bold mb-2">Degree: <span
                    class="font-light">{{ $this->application->profile->degree ?? 'Not Specified' }}</span>
            </p>


            <p class="font-bold mb-2">Occupation: <span
                    class="font-light">{{ $this->application->profile->occupation ?? 'Not Specified' }}</span>
            </p>
        </div>



        <div class="p-8 tab-pane hidden" id="tab-information-skills">
            @foreach ($this->application->profile->filters as $filter)
                <div class="mb-2">
                    <p class="font-bold mb-2">{{ $filter['name'] }}:</p>

                    @foreach ($filter['options'] as $option)
                        <div class="px-3 py-2 rounded-md  bg-py-blue-200 inline-block">
                            <p class="font-medium text-py-blue text-small">{{ $option }}</p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="p-8 tab-pane hidden flex justify-center" id="tab-introduction-video">
            @if ($this->application->profile->video)
                <video class="h-auto m-auto" controls loading="lazy" id="talent-video">
                    <source src="{{ $this->application->profile->video }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                <p class="text-center">No video uploaded</p>
            @endif
        </div>


        <div class="p-8 tab-pane hidden" id="tab-photos">
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 sm:gap-4">
                @foreach ($this->application->profile->profileImages as $image)
                    <div class="relative image-viewer">
                        <img src="{{ $image->image }}" class="rounded-md"
                            wire:click="showImage('{{ $image->image }}')" />
                    </div>
                @endforeach
            </div>
        </div>

        @if (auth()->user()->hasPermissionTo('view talent previous commercials'))
            <div class="p-8 tab-pane hidden" id="tab-previous-commercials">
                @if ($this->application->profile->previous_commercials)
                    <p class="font-bold mb-2">Previous Commercials:</p>
                    <ul>
                        @foreach ($this->application->profile->previous_commercials as $commercial)
                            <li class="list-disc">
                                <span class="font-light">{{ $commercial }}</span>
                            </li>
                        @endforeach
                    @else
                        <p class="text-center mb-2">No previous commercials</p>
                @endif
                </ul>
            </div>
        @endif
    </div>

    @if ($this->showImageViewer)
        <div id="image-preview">
            <span class="close" wire:click="$set('showImageViewer', false)">&times;</span>
            <img class="modal-content" id="full-image" src="{{ $this->selectedImage }}">
        </div>
    @endif
</x-filament-panels::page>

@push('scripts')
    <script>
        loadVideo();

        // Function to dynamically set video source and load it
        function loadVideo() {
            var video = document.getElementById('talent-video');

            if (!video) return;

            var source = document.createElement('source');
            source.setAttribute('src', 'movie.mp4');
            source.setAttribute('type', 'video/mp4');
            video.appendChild(source);
            // Load the video now
            video.load();
        }


        document.addEventListener('DOMContentLoaded', function() {
            var tabs = document.querySelectorAll('.tab-button');


            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var previousTab = document.querySelector('.tab-button.border-b-py-blue');
                    previousTab.classList.remove('border-b-py-blue', 'border-b-2');

                    var targetId = this.getAttribute('data-target');

                    var panes = document.querySelectorAll('.tab-pane');
                    tab.classList.add('border-b-2', 'border-b-py-blue');

                    panes.forEach(function(pane) {
                        pane.style.display = 'none';
                    });

                    document.getElementById(targetId).style.display = 'block';
                });
            });

        });
    </script>
@endpush
