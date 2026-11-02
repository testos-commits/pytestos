<div>
    @if ($backLink)
        <a href="{{ $backLink }}" class="mb-3 block md:-ml-4 hover:text-py-blue mt-4 md:mt-0">
            <x-heroicon-s-chevron-left class="h-4 inline mb-1" />
            {{ $backText }}
        </a>
    @endif
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-2">

        <div class="mb-4 md:mb-0">
            <h1 class="text-md lg:text-2xl font-medium text-py-blue ">{{ $title }}</h1>
            <p class="text-sm lg:text-base text-black font-medium">{{ $subtitle }}</p>
        </div>
        <div class="flex flex-col md:flex-row md:w-[60%]">
            <input type="text"
                class="flex-1 mr-4 rounded-md border border-white placeholder:text-gray-700 mb-4 md:mb-0 text-sm lg:text-base"
                wire:model.live.debounce.300ms="searchQuery" placeholder="Search applicants..." />

            <div class="flex flex-row justify-between">
                @if (auth()->user()->hasPermissionTo('access talent messages'))
                    <a href="{{ route('filament.admin.resources.messages.audition-message-chats', ['record' => $this->audition->id]) }}"
                        class="mr-4 btn flex flex-row border border-py-blue group hover:bg-py-blue items-center">
                        <x-icons.chat class="fill-py-blue group-hover:fill-white " />
                        <span class="ml-2 text-py-blue group-hover:text-white">Inbox</span>
                    </a>
                @endif

                <div class="relative">
                    <button wire:ignore id="js-manage-audition-actions-trigger"
                        class="border-py-blue border rounded-md flex h-10 flex-row justify-between items-center">
                        <div class="px-6 text-py-blue text-sm md:text-base ">
                            Actions
                        </div>
                        <div class="bg-py-blue py-2 px-1 h-full flex items-center rounded-r-sm border-2 border-py-blue">
                            <x-icons.chevron-down />
                        </div>
                    </button>
                    {{-- @if ($this->actionsOpen) --}}
                    <div class="absolute bg-white w-56 top-[42px] right-0 rounded-md z-10 hidden"
                        id="js-manage-audition-actions" wire:ignore>
                        <ul>
                            @if (auth()->user()->hasPermissionTo('change application status') || auth()->user()->hasPermissionTo('shortlist applicant'))
                                <li wire:click="changeSelectedApplicationStatus('shortlist')"
                                    class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base">
                                    <x-icons.shortlist-all />
                                    <span class="ml-2">Shortlist Selected</span>
                                </li>

                                <li wire:click="changeSelectedApplicationStatus('unshortlist')"
                                    class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base">
                                    <x-icons.shortlist-all />
                                    <span class="ml-2">Unshortlist Selected</span>
                                </li>
                            @endif

                            @if (auth()->user()->hasPermissionTo('change application status'))
                                <li wire:click="changeSelectedApplicationStatus('unaccept')"
                                    class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base">
                                    <x-icons.shortlist-all />
                                    <span class="ml-2">Unaccept Selected</span>
                                </li>


                                <li wire:click="changeSelectedApplicationStatus('accept')"
                                    class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base">
                                    <x-icons.accept-all />
                                    <span class="ml-2">Accept Selected</span>
                                </li>
                            @endif
                            {{-- <li wire:click="toggleActions()"
                                    class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base">
                                    <x-icons.download-all />
                                    <span class="ml-2">Download All</span>
                                </li> --}}

                            <li wire:click="downloadSelectedProfiles()"
                                class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base rounded-b-md">
                                <x-icons.download-selected />
                                <span class="ml-2">Download Selected</span>

                            <li wire:click="changeSelectedApplicationStatus('select')"
                                class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base rounded-b-md">
                                <x-icons.download-selected />
                                <span class="ml-2">Add To Selection</span>

                            <li wire:click="changeSelectedApplicationStatus('unselect')"
                                class="flex flex-row items-center text-py-blue py-3 w-full px-4  hover:bg-py-blue-200 cursor-pointer text-sm md:text-base rounded-b-md">
                                <x-icons.download-selected />
                                <span class="ml-2">Remove From Selection</span>
                            </li>
                        </ul>
                    </div>
                    {{-- @endif --}}
                </div>
            </div>

        </div>
    </div>

    <div class="flex flex-row justify-between overflow-x-scroll md:overflow-x-visible my-4 no-scrollbar">
        <div class="flex flex-row justify-between">
            @if (auth()->user()->hasPermissionTo('view applicants'))
                <button wire:click="setActiveTab('applicants')"
                    class="btn min-w-[130px] {{ $this->activeTab === 'applicants' ? 'tab-active' : 'tab' }} mr-2">Applied
                    ({{ $this->audition->applicants_count }})</button>
            @endif
            <button wire:click="setActiveTab('shortlisted')"
                class="btn min-w-[130px] mr-2 {{ $this->activeTab === 'shortlisted' ? 'tab-active' : 'tab' }}">Shortlisted
                ({{ $this->audition->shortlisted_count }})</button>
            @if (auth()->user()->hasPermissionTo('view accepted applicants'))
                <button wire:click="setActiveTab('accepted')"
                    class="btn min-w-[130px] mr-2 {{ $this->activeTab === 'accepted' ? 'tab-active' : 'tab' }}">Accepted
                    ({{ $this->audition->accepted_count }})</button>
            @endif
            <button wire:click="setActiveTab('selection-list')"
                class="btn min-w-[130px] mr-2 {{ $this->activeTab === 'selection-list' ? 'tab-active' : 'tab' }}">Selection
                List
                ({{ $this->audition->selected_count }})</button>
        </div>

        <div class="flex flex-row">
            <button wire:click="selectApplicantsInTab()" class="btn min-w-[130px] mr-2 tab">Select All
                {{ $this->getActiveTabLabel() }}</button>

            <button wire:click="clearSelection()" class="btn min-w-[130px] mr-2 tab-active">Clear Selected Applicants
            </button>

            <button class="btn min-w-[130px] bg-white flex flex-row items-center text-gray-800 ml-2"
                wire:click="set('showFilters', true)">
                <x-monoicon-filter class="h-5 text-gray-800 mr-2" />
                Filter
            </button>
        </div>
    </div>
</div>
