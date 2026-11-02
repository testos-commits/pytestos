<div>
    <x-layouts.header :$title :$buttonText :$buttonLink :$placeholder :$subtitle/>
</div>

<div class="flex flex-row justify-end">
    <button class="btn bg-py-blue flex flex-row items-center text-white" wire:click="set('showFilters', true)">
        <x-monoicon-filter class="h-5 text-white mr-2" />
        Filter
    </button>
</div>
