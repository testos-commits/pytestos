<div wire:click="setActiveProfile({{ $application->id }})"
    class="flex flex-row justify-between items-center py-2 px-2 hover:bg-py-blue-200 cursor-pointer rounded-sm text-sm
    {{ $activeProfile?->id === $application->profile->id ? 'bg-py-blue-200' : '' }}">

    <x-messages.profile-chat-header :name="$application->profile->first_name" :image="$application->profile->getAvatar()" />

    <div class="flex flex-col items-end">
        <p class="text-xs mb-2">
            {{-- {{ $application?->profile?->messages?->first()?->pivot?->updated_at?->diffForHumans() }}</p> --}}

            @if ($application->unread_by_admin > 0)
                <div class="rounded-full h-5 w-5 bg-py-blue flex items-center justify-center">
                    <p class="text-white">{{ $application->unread_by_admin }}</p>
                </div>
            @endif
    </div>
</div>
