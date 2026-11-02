<x-filament-panels::page>
    <div class="grid grid-cols-2 md:grid-cols-[repeat(4,_1fr)] gap-4">
        @if ($this->activeTab === 'applicants')
            @if ($this->audition->applicants->count() == 0)
                <p class="text-sm text-gray-900  mb-2">No applicants yet</p>
            @endif
            @foreach ($this->audition->applicants as $user)
                <x-auditions.applicant-card :viewMode="false" :url="'/admin/profiles/application/' . $user->pivot->id . '?pool=applicants'" :profile="$user" />
            @endforeach
        @elseif($this->activeTab === 'shortlisted')
            @if ($this->audition->shortlisted->count() == 0)
                <p class="text-sm text-gray-900  mb-2">No shortlisted users yet</p>
            @endif
            @foreach ($this->audition->shortlisted as $user)
                <x-auditions.applicant-card :viewMode="false" :url="'/admin/profiles/application/' . $user->pivot->id . '?pool=shortlisted'" :profile="$user" />
            @endforeach
        @elseif ($this->activeTab === 'accepted')
            @if ($this->audition->accepted->count() == 0)
                <p class="text-sm text-gray-900  mb-2">No accepted applications yet</p>
            @endif
            @foreach ($this->audition->accepted as $user)
                <x-auditions.applicant-card :viewMode="false" :url="'/admin/profiles/application/' . $user->pivot->id . '?pool=accepted'" :profile="$user" />
            @endforeach
        @elseif ($this->activeTab === 'selection-list')
            @if ($this->audition->selected->count() == 0)
                <p class="text-sm text-gray-900  mb-2">No selected applicants yet</p>
            @endif
            @foreach ($this->audition->selected as $user)
                <x-auditions.applicant-card :viewMode="false" :url="'/admin/profiles/application/' . $user->pivot->id . '?pool=selected'" :profile="$user" />
            @endforeach
        @endif
    </div>


    @if ($this->showFilters)
        <livewire:profile-filter :data="$this->filtersData" />
    @endif
</x-filament-panels::page>
