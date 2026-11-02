
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div>
        <div class="mb-8">
        
            <input type="hidden" {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" />
            <div class="py-4 px-2 max-h-[400px] h-full overflow-y-scroll border rounded-md">
                @if (count($this->applications) == 0)
                    <p class="text-gray-400 text-sm">No available applicants </p>
                @endif

                @foreach ($this->applications as $k => $application)
                    <div class="mb-2 hover:bg-py-blue-200 px-2 py-2 rounded-md cursor-pointer"
                        wire:click="addToSelectedRecipients({{ $k }})" :key="'a'.$k">
                        <x-messages.profile-chat-header :image="$application->profile->getAvatar()" :name="'#'.$application->profile->id . ' ' . $application->profile->first_name" />
                    </div>
                @endforeach
            </div>


        </div>


        <div>
            <p class="text-sm mb-4">Selected </p>

            <div class="py-4 px-2 max-h-[400px] h-full overflow-y-scroll border rounded-md">
                @if (count($this->selectedRecipients) == 0)
                    <p class="text-gray-400 text-sm">No recipients selected</p>
                @endif

                @foreach ($this->selectedRecipients as $k => $recipient)
                    <div class="mb-2 hover:bg-py-blue-200 px-2 py-2 rounded-md cursor-pointer flex flex-row justify-between items-center"
                        wire:click="removeFromSelectedRecipients({{ $k }})" :key="'r'.$k">
                        <x-messages.profile-chat-header :image="$recipient->profile->getAvatar()"  :name="'#'.$recipient->profile->id . ' ' . $recipient->profile->first_name" />
                        <x-icons.delete-orange />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-dynamic-component>
