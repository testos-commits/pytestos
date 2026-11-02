<div class="flex fex-row items-center w-max ">
    @if ($image)
        <img src="{{ $image }}" alt=""
            class="w-10 h-10 rounded-full" />
    @else
        <div class="w-10 h-10 rounded-full bg-py-blue flex justify-center items-center">
            <p class="font-bold text-lg text-white">{{ substr($name, 0, 1) }}</p>
        </div>
    @endif
    <p class="ml-2">{{ $name }}</p>
</div>