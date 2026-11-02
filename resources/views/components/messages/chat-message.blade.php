@php 
$borderBottom = '';

if ($message->options && $message->options->count() > 0) {
    $borderBottom = 'border-b pb-2';
}
@endphp
<div class="bg-py-gray px-4 py-6 mb-3">
    <div class="{{$borderBottom}}">
        <p class="font-bold text-base">{{ $message->message }}</p>
    </div>

    @if ($message->options && $message->options->count() > 0)
        <div class="py-2">
            <p class="text-sm font-bold text-gray-400">Available Answers:</p>
            <div class="py-2">
                @foreach ($message->options as $option)
                    <div class="flex flex-row items-center mb-1">
                        <div class="h-4 w-4 rounded-full border-2 border-py-blue flex items-center justify-center">
                            @if ($message->pivot->audition_messages_option_id == $option->id)
                                <div class="h-2 w-2 rounded-full bg-py-blue"></div>
                            @endif
                        </div>
                        <p class="text-sm ml-2 pb-2">
                            {{ $option->option }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
