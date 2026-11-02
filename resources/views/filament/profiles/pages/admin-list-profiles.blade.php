<x-filament-panels::page>
    <div class="grid grid-cols-[repeat(2,_1fr)]  md:grid-cols-[repeat(4,_1fr)] gap-4">

        @if (sizeof($this->profiles) == 0)
            <p class="text-sm text-gray-900  mb-2">No profiles yet</p>
        @endif
        @foreach ($this->profiles as $profile)
            <x-auditions.applicant-card
                url="{{ route('filament.admin.resources.profiles.view', ['record' => $profile->id]) }}" 
                :profile="$profile"
                viewMode="true"
                />
        @endforeach
    </div>

    <x-filament::pagination :paginator="$this->profiles" />

    @if ($this->showFilters)
        <livewire:profile-filter :data="$this->filtersData" />
    @endif
</x-filament-panels::page>
