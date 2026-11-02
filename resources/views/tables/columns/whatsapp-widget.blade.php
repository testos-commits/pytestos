<div class="flex items-end justify-center align-center h-full">
    @php
        $phone = $getRecord()->profile->phone;
        $size = $size ?? 5;
    @endphp

    @if ($phone)
        <a href="https://wa.me/{{ $phone }}" target="_blank">
            <x-ri-whatsapp-fill class="h-{{ $size }} fill-green-400" />
        </a>
    @endif
</div>
