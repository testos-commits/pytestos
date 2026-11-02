<x-filament-panels::page>
    <div>
        @if ($this->audition->messages->count() == 0)
            <p class="text-sm text-gray-900 text-center mb-2">No messages found.</p>
        @endif

        @foreach ($this->audition->messages as $message)
            <div class="bg-white p-8 rounded-md">
                <div class="flex justify-between items-center">
                    <p class="font-bold">{{ $message->message }} </a>
                    <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                </div>
                <div class="mt-4">
                    <p class="text-gray-400">Filter users by answer <span
                            class="font-normal text-sm text-gray-400">({{ $message->targeted }})</span></p>
                    <div class="mt-2">
                        @foreach ($message->options as $option)
                            <a
                                href="{{ route('filament.admin.resources.messages.answer-profiles', [
                                    'answerId' => $option->id,
                                    'auditionId' => $this->audition->id,
                                ]) }}">
                                <x-auditions.tag :text="$option->option" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
