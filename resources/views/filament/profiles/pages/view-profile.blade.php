<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-[minmax(70%,_1fr)_1fr] gap-8 mb-8">
        <div class="owl-carousel owl-theme" id="image-container" wire:ignore>
            @foreach ($profile->castingImages as $k => $image)
                <div class="item bg-black">
                    <div class="h-[600px] w-full bg-no-repeat bg-center bg-contain"
                        style="background-image: url('{{ $image->image }}')"></div>

                </div>
            @endforeach
        </div>
        <div class="bg-white rounded-md w-full py-8 px-8 text-lg h-fit">
            <h2 class="font-bold mb-4">Account Details</h2>

            <div class="mb-2">
                <p class="font-bold text-base">Owner email</p>
                <p class="text-base">{{ $profile->user->email }}</p>
            </div>

            <div class="mb-2">
                <p class="font-bold text-base">Joined</p>
                <p class="text-base">{{ $profile->user->created_at }}</p>
            </div>

            <div class="mb-2">
                <p class="font-bold text-base">Profile name</p>
                <p class="text-base">{{ $profile->first_name }} {{ $profile->last_name }}</p>
            </div>

            <div class="mb-2">
                <p class="font-bold text-base">Phone</p>
                <p class="text-base">{{ $profile->phone ?? 'Not Specified' }}</p>
            </div>

            <div class="mb-2">
                <p class="font-bold text-base">Alternative Phone Number</p>
                <p class="text-base">{{ $profile->alternative_phone_number ?? 'Not Specified' }}</p>
            </div>


            <div class="mb-2">
                <p class="font-bold text-base">Referral Code</p>
                <p class="text-base">{{ $profile->user->referral_code ?? 'Not Created' }}</p>
            </div>

            @if ($profile->user->referredBy)
                <div class="mb-2">
                    <p class="font-bold text-base">Referred By</p>
                    <p class="text-base">#{{ $profile->user->referredBy->id }}, {{ $profile->user->referredBy->email }}
                    </p>
                </div>
            @endif

        </div>
    </div>

    <div class="bg-white py-8 px-8 ">
        <div class="w-full  border-b-2 border-b-py-blue-200 flex flex-row overflow-x-scroll">
            @foreach ($this->tabs as $k => $tab)
                <button data-target="tab-{{ $k }}"
                    class="w-[300px] text-base
                            font-bold py-2 px-4
                            border-b-2 border-transparent 
                            hover:border-b-py-blue
                            {{ $k == 'about' ? 'border-b-py-blue' : '' }}
                            focus:outline-none tab-button tab-{{ $k }}">{{ $tab }}</button>
            @endforeach
        </div>


        <div class="p-8 tab-pane " id="tab-about">

            <p class="font-bold mb-2">User Reference: <span class="font-light">#{{ $this->profile->id }}</span>
            </p>


            <p class="font-bold mb-2">Name: <span class="font-light">{{ $this->profile->first_name }}
                    {{ $this->profile->last_name }}</span>
            </p>

            <p class="font-bold mb-2">Age: <span class="font-light">{{ $this->profile->age }}
                    y/o</span>
            </p>

            @if (auth()->user()->hasPermissionTo('view talent description'))
                <div class="mb-4">
                    <p class="font-bold mb-2">Description: </p>
                    <span class="font-light">{{ $profile->description }}</span>
                </div>
            @endif

            @if ($profile->social_media_accounts)
                <div class="mb-4">
                    <p class="font-bold mb-2">Social Media: </p>
                    @foreach ($profile->social_media_accounts as $k => $account)
                        <p class="font-medium">{{ ucfirst($k) }}: <span
                                class="font-light">{{ $account }}</span></p>
                    @endforeach
                </div>
            @endif

            <p class="font-bold mb-2">Country: <span class="font-light">{{ $this->profile->country->name }}</span>
            </p>

            <p class="font-bold mb-2">Nationality: <span
                    class="font-light">{{ $this->profile->adminDisplayNationalities() }}</span>
            </p>
            <p class="font-bold mb-2">Passport: <span
                    class="font-light">{{ $this->profile->adminDisplayPassports() }}</span>
            </p>
            <p class="font-bold mb-2">Shoes Size: <span class="font-light">{{ $this->profile->shoes }}</span>
            </p>

            <p class="font-bold mb-2">Shirt Size: <span class="font-light">{{ $this->profile->shirt }}</span>

            <p class="font-bold mb-2">Pants Size: <span class="font-light">{{ $this->profile->pants }}</span>
            </p>

            <p class="font-bold mb-2">Weight: <span class="font-light">{{ $this->profile->weight }}</span>
            </p>

            <p class="font-bold mb-2">Height: <span class="font-light">{{ $this->profile->height }}</span>
            </p>


            <p class="font-bold mb-2">Gender: <span class="font-light">{{ $this->profile->gender }}</span>
            </p>


            <p class="font-bold mb-2">University: <span
                    class="font-light">{{ $this->profile->university ?? 'Not Specified' }}</span>
            </p>


            <p class="font-bold mb-2">Degree: <span
                    class="font-light">{{ $this->profile->degree ?? 'Not Specified' }}</span>
            </p>


            <p class="font-bold mb-2">Occupation: <span
                    class="font-light">{{ $this->profile->occupation ?? 'Not Specified' }}</span>
            </p>

            <div class="mb-2">
                <p class="font-bold text-base">City</p>
                <p class="text-base">{{ $profile->city ?? 'Not Specified' }}</p>
            </div>

            <div class="mb-2">
                <p class="font-bold text-base">Street</p>
                <p class="text-base">{{ $profile->street ?? 'Not Specified' }}</p>
            </div>

            <div class="mb-2">
                <p class="font-bold text-base">Building</p>
                <p class="text-base">{{ $profile->building ?? 'Not Specified' }}</p>
            </div>
        </div>



        <div class="p-8 tab-pane hidden" id="tab-information-skills">
            @foreach ($this->profile->filters as $filter)
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
            @if (!$this->profile->video)
                <p class="text-center">No video uploaded</p>
            @else
                <video class="h-auto m-auto" controls loading id="myVideo">
                    <source type="video/mp4" source="{{ $this->profile->video }}">
                    Your browser does not support the video tag.
                </video>
            @endif
        </div>

        <div class="p-8 tab-pane hidden" id="tab-photos">
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 sm:gap-4">
                @foreach ($this->profile->profileImages as $image)
                    <div class="relative image-viewer">
                        <img src="{{ $image->image }}" class="rounded-md"
                            wire:click="showImage('{{ $image->image }}')" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-8 tab-pane hidden" id="tab-previous-commercials">
            @if (!$this->profile->previous_commercials)
                <p class="text-center">No previous commercials</p>
            @else
                @foreach ($this->profile->previous_commercials as $commercial)
                    <div class="mb-2">
                        <p class="font-bold mb-2"><span class="font-light">{{ $commercial }}</span></p>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="p-8 tab-pane hidden" id="tab-network">
            @if ($this->profile->user->referrals->count() == 0)
                <p class="text-center">No referrals</p>
            @endif
            @foreach ($this->profile->user->referrals as $referrals)
                <div class="mb-2">
                    <p class="font-bold mb-2">#{{ $referrals->id }} - <span
                            class="font-light">{{ $referrals->email }}</span></p>
                </div>
            @endforeach
        </div>


        <div class="p-8 tab-pane hidden" id="tab-bank_details">
            @if ($profile->user->paymentDetails !== null)
                <p class="font-bold mb-2">Name: <span
                        class="font-light">{{ $this->profile->user->paymentDetails->name }}</span>
                </p>

                <p class="font-bold mb-2">Bank Name: <span
                        class="font-light">{{ $this->profile->user->paymentDetails->bank_name }}</span>
                </p>


                <p class="font-bold mb-2">Branch: <span
                        class="font-light">{{ $this->profile->user->paymentDetails->branch }}</span>
                </p>

                <p class="font-bold mb-2">IBAN: <span
                        class="font-light">{{ $this->profile->user->paymentDetails->iban }}</span>
                </p>

                <p class="font-bold mb-2">SWIFT: <span
                        class="font-light">{{ $this->profile->user->paymentDetails->swift }}</span>
                </p>
            @else
                <p class="text-center">No bank details provided</p>
            @endif
        </div>
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
        // Example array of image URLs
        const imageUrls = @json($this->profile->castingImages->pluck('image'));
        const profileVideo = @json($this->profile->video);
        // Function to dynamically load an image
        document.addEventListener('DOMContentLoaded', function() {
            function loadImage(url) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.onerror = reject;
                    img.src = url;
                });
            }

            // Load all images and append them to the DOM
            Promise.all(imageUrls.map(url => loadImage(url))).then(images => {
                const container = document.getElementById('image-container');

                images.forEach((img, index) => {
                    // Create a new div element for this image
                    const div = document.createElement('div');
                    div.classList.add('hidden', 'duration-700', 'ease-in-out', 'flex',
                        'justify-center');


                    if (imageUrls.length == 1) {
                        div.classList.add('!translate-x-0')
                    }

                    if (index === 0) {
                        div.setAttribute('data-carousel-item', 'active');
                    } else {
                        div.setAttribute('data-carousel-item', '');
                    }


                    // Append the image to the div
                    div.appendChild(img);

                    // Append the div to the container
                    container.appendChild(div);
                });

                // After all images are loaded, load the video
                loadVideo();
            }).catch(error => console.error("Error loading images:", error)).finally(() => {
                // Initialize the carousel
                const container = document.getElementById('image-container');
                const firstDiv = container.querySelector('[data-carousel-item]');
            })

            // Function to dynamically set video source and load it
            function loadVideo() {
                if (!profileVideo) return;
                var video = document.getElementById('myVideo');
                var source = document.createElement('source');
                source.setAttribute('src', profileVideo);
                source.setAttribute('type', 'video/mp4');
                video.appendChild(source);
                // Load the video now
                video.load();
            }


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


    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

    {{-- <script src="{{ asset('js/flowbite/flowbite.js') }}"></script> --}}
@endpush
