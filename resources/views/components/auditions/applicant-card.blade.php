@php
    $name = "";

    if (auth()->user()->hasPermissionTo('view talent full name')) {
        $name = $profile->full_name;
    }

     if (auth()->user()->hasPermissionTo('view talent first name') && !auth()->user()->hasPermissionTo('view talent full name')) {
        $name = $profile->first_name;
    }

    $age = $profile->age;
    $image = $profile->getAvatar();
    $id = $profile->id;
    $old_id = $profile->old_user_id ?? null;
    $mobile = $profile->clean_mobile;
    $in_selection_list = $profile->pivot->in_selection ?? false;
    $isBlackListed = $profile->is_blacklisted;
    $updated_at = null;

    if ($profile->pivot) {
        $updated_at =
            $profile->pivot->updated_at->diffForHumans([
                'parts' => 3,
                'join' => ', ',
                'syntax' => Carbon\CarbonInterface::DIFF_ABSOLUTE,
            ]) . ' ago';
    }

@endphp

<div class="pb-2 rounded-t bg-white rounded-md relative">
    <div class="flex flex-row align-center justify-between absolute top-4 left-4 z-10">
        <div>
            @if (isset($viewMode) && !$viewMode && $this->isApplicantSelected($id))
                <button x-on:click="$wire.unselectApplicant({{ $id }})">
                    <x-icons.selected-checkmark />
                </button>
            @endif

            @if (isset($viewMode) && !$viewMode && !$this->isApplicantSelected($id))
                <button x-on:click="$wire.selectApplicant({{ $id }})">
                    <div class="bg-white h-6 w-6 sm:h-8 sm:w-8 rounded-full border border-2 border-py-blue">
                    </div>
                </button>
            @endif
        </div>

        <div>
            @if ($isBlackListed)
                <x-solar-danger-bold class="h-6 w-6 sm:h-8 sm:w-8" />
            @endif
        </div>
    </div>
    <a href="{{ $url }}" target="_blank">
        <div style="background-image: url('{{ $image }}');"
            class="bg-no-repeat bg-center bg-contain h-[150px] md:h-[230px] w-full rounded-t-md relative">
            <div class="absolute top-4 px-4 w-full">

            </div>
        </div>
    </a>
    <div class="text-center pt-2">
        <a href="{{ $url }}" target="_blank">
            <p class="text-sm md:text-lg text-black font-medium">{{ $name ? $name . ', ' : '' }} {{ $age ?  $age . ' y/o ' : '' }}</p>
        </a>
        <p class="text-base text-py-orange">#{{ $id }} <span
                class="text-sm text-gray-400">{{ $old_id ? "(Old id: #$old_id)" : '' }}</span>
        </p>
    </div>


    @if (isset($viewMode) && $viewMode)
        <div class="flex justify-between w-full px-4 pb-4">
            <div>
                @if ($mobile && auth()->user()->hasPermissionTo('view talent phone details'))
                    <a href="https://wa.me/{{ $mobile }}" target="_blank">
                        <x-ri-whatsapp-fill class="h-5 fill-green-400" />
                    </a>
                @endif
            </div>
            @if (auth()->user()->hasPermissionTo('edit talent profile'))
                <a href="{{ route('filament.admin.resources.profiles.edit', ['record' => $id]) }}">
                    <x-heroicon-o-pencil-square class="h-5" />
                </a>
            @else
                <div></div>
            @endif
        </div>
    @endif

    @if (isset($viewMode) && !$viewMode)
        <div
            class="flex {{ auth()->user()->hasPermissionTo('change application status') ? 'justify-between' : 'justify-center' }} w-full px-2 sm:px-10 py-2 items-center">

            @if (
                !auth()->user()->hasPermissionTo('change application status') &&
                    auth()->user()->hasPermissionTo('shortlist applicant'))

                @if ($this->activeTab == 'shortlisted' && $this->activeTab !== 'selection-list')
                    <button wire:click="changeApplicationStatus('downgrade', {{ $id }})">
                        <x-ri-arrow-left-circle-fill class="h-6 w-6 sm:h-8 sm:w-8 fill-py-blue" />
                    </button>
                @endif
            @endif

            @if (
                !auth()->user()->hasPermissionTo('change application status') &&
                    auth()->user()->hasPermissionTo('shortlist applicant'))
                @if ($this->activeTab == 'applicants' && $this->activeTab !== 'selection-list')
                    <button wire:click="changeApplicationStatus('upgrade', {{ $id }})">
                        <x-ri-arrow-right-circle-fill class="h-6 w-6 sm:h-8 sm:w-8 fill-py-green" />
                    </button>
                @endif
            @endif

            @if (auth()->user()->hasPermissionTo('change application status'))
                @if ($this->activeTab !== 'applicants' && $this->activeTab !== 'selection-list')
                    <button wire:click="changeApplicationStatus('downgrade', {{ $id }})">
                        <x-ri-arrow-left-circle-fill class="h-6 w-6 sm:h-8 sm:w-8 fill-py-blue" />
                    </button>
                @endif

                @if ($this->activeTab !== 'accepted' && $this->activeTab !== 'selection-list')
                    <button wire:click="changeApplicationStatus('upgrade', {{ $id }})">
                        <x-ri-arrow-right-circle-fill class="h-6 w-6 sm:h-8 sm:w-8 fill-py-green" />
                    </button>
                @endif

                <a
                    href="{{ route('filament.admin.resources.messages.create', ['auditionId' => $this->auditionId, 'recipientId' => $id]) }}">
                    <x-monoicon-message-alt class="h-6 w-5 sm:h-8 sm:w-7 fill-py-purple" />
                </a>
            @endif
            @if (!$in_selection_list)
                <button wire:click="changeApplicationStatus('select', {{ $id }})">
                    <x-icons.add />
                </button>
            @else
                <button wire:click="changeApplicationStatus('unselect', {{ $id }})">
                    <x-ri-delete-bin-5-line class="h-6 w-5 sm:h-8 sm:w-7 -mt-1  fill-py-orange" />
                </button>
            @endif

            @if ($mobile && auth()->user()->hasPermissionTo('view talent phone details'))
                <div>
                    <a href="https://wa.me/{{ $mobile }}" target="_blank">
                        <x-ri-whatsapp-fill class="h-5 sm:h-7 fill-green-400" />
                    </a>
                </div>
            @endif

        </div>
    @endif

    @if ($updated_at !== null)
        <div>
            <p class="text-center text-sm text-gray-400">({{ $updated_at }})</p>
        </div>
    @endif
</div>
