<div>
    <div class="flex flex-row items-center">
        {{ $slot }}
    
        <p class="text-py-blue font-medium text-sm ml-1 capitalize">{{ $label }}</p>
    </div>
    <p class="text-gray-800 font-medium text-sm capitalize">{{ $value }} </p>
</div>