@php
    $canEditProject = auth()->user()->hasPermissionTo('edit projects');
@endphp

<x-filament-panels::page>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ($this->projects as $project)
            <div class="bg-white radius-md py-4 px-6 rounded-md {{$project->trashed() ? 'opacity-60' : ''}}">
                <div class="flex flex-row mb-4 items-center">
                    <a class="font-medium text-py-blue lg:text-lg md:text-base text-sm mr-2"
                        href="{{ $canEditProject ? route('filament.admin.resources.projects.edit', ['record' => $project->id]) : '#'  }}">
                        #{{ $project->id }}
                        {{ Str::length($project->title) > 38 ? substr($project->title, 0, 38) . '...' : $project->title }} 
                        {!!$project->trashed() ? '<span class="text-xs">(Removed)</span>' : ''!!}
                    </a>
                    @if ($canEditProject)
                        <x-filament::icon icon="heroicon-o-pencil" wire:target="search"
                            class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                    @endif
                </div>

                <div class="border-t border-b border-t-gray-300 border-b-gray-300 py-4 grid grid-cols-2 gap-4">
                    <div>
                        <div class="flex flex-row items-center">
                            <x-icons.audition class="h-5 w-5" />
                            <p class="text-py-blue font-medium text-sm ml-1">Auditions</p>
                        </div>
                        <p class="text-gray-800 font-medium text-sm">{{ $project->auditions->count() }}</p>
                    </div>
                    @if(auth()->user()->hasPermissionTo('view applicants'))
                    <div>
                        <div class="flex flex-row items-center">
                            <x-icons.paid class="h-5 w-5" />
                            <p class="text-py-green font-medium text-sm ml-1">Applied</p>
                        </div>

                        <p class="text-gray-800 font-medium text-sm">{{ $project->applied_count }}</p>
                    </div>
                    @endif

                    <div>
                        <div class="flex flex-row items-center">
                            <x-icons.time class="h-5 w-5" />
                            <p class="text-py-orange font-medium text-sm ml-1">Shortlisted</p>
                        </div>

                        <p class="text-gray-800 font-medium text-sm">{{ $project->shortlisted_count }}</p>
                    </div>

                    @if(auth()->user()->hasPermissionTo('view accepted applicants'))
                    <div>
                        <div class="flex flex-row items-center">
                            <x-icons.checkmark class="h-5 w-5" />
                            <p class="text-py-purple font-medium text-sm ml-1">Accepted</p>
                        </div>

                        <p class="text-gray-800 font-medium text-sm">{{ $project->accepted_count }}</p>
                    </div>
                    @endif
                </div>

                <div class="my-4">
                    <a href="/admin/auditions?projectId={{ $project->id }}"
                        class="btn bg-py-blue text-white w-full text-center border-py-blue block hover:bg-white hover:text-py-blue">
                        Manage Auditions
                    </a>

                    @if (auth()->user()->hasPermissionTo('manage accounting'))
                        <a href="{{ route('filament.admin.resources.payments.index', ['record' => $project->id]) }}"
                            class="btn bg-white text-py-blue border-py-blue w-full text-center block hover:bg-py-blue hover:text-white  mt-4">
                            Accounting
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
