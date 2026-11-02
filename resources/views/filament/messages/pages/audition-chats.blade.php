<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-[1fr_minmax(60%,_1fr)]">
        <div class="sm:h-chat overflow-y-scroll mb-4 sm:mb-0">
            <p class="text-sm text-gray-300 mb-2">ALL MESSAGES</p>

            <div class="flex flex-row overflow-x-scroll sm:block gap-4 w-full">
                @foreach ($this->audition->applications as $application)
                    <x-messages.profile-chat :$application :$activeProfile />
                @endforeach
            </div>

            @if ($this->audition->applications->count() == 0)
                <p class="text-sm text-gray-900 text-center mb-2">No chats yet</p>
            @endif
        </div>

        <div class="sm:p-10">
            @if ($this->activeProfile)
                <div class="overflow-y-scroll bg-white h-chat p-4 sm:p-8">
                    <div class="flex flex-row justify-between items-center sm:p-4 mb-4">
                        <x-messages.profile-chat-header :name="$this->activeProfile?->first_name" :image="$this->activeProfile?->getAvatar()" />

                        <a href="{{ route('filament.admin.resources.messages.create', [
                            'auditionId' => $this->audition->id,
                            'recipientId' => $this->activeProfile->id,
                        ]) }}"
                            class="text-py-blue flex flex-row text-small ">
                            <x-icons.letter class='mr-2' />
                            Message {{ $this->activeProfile->first_name }}
                        </a>

                    </div>
                    @foreach ($this->activeProfile->messages as $message)
                        <x-messages.chat-message :$message />
                    @endforeach
                </div>
        </div>
        @endif
    </div>

</x-filament-panels::page>
