<div class="fixed top-0 bottom-0 right-0 left-0 bg-black/50 flex items-center justify-center py-10 z-50">
    <div class="bg-gray-50 rounded-md px-6 py-8  h-full overflow-y-scroll min-w-24 z-40">
        <h1 class="mb-4 text-xl font-bold">Filter profiles</h1>
        <form wire:submit="create">
            {{ $this->form }}

            <div class="flex flex-row justify-between items-center mt-4 block">
                <div>
                    <button class="btn bg-py-blue text-white" type="submit">
                        Apply
                    </button>
                    <button class="btn bg-py-blue text-white" type="button" wire:click="resetFilters">
                        Reset
                    </button>
                </div>

                <button class="btn bg-py-blue text-white" type="button" wire:click="$dispatch('hide-filters')">
                    Cancel
                </button>
            </div>
        </form>

    </div>
</div>
