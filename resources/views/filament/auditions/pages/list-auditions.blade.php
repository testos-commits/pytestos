<x-filament-panels::page>
    <div>
        @if ($this->auditions->count() == 0)
            <p class="text-lg text-gray-900  mt-5">No auditions found</p>
        @endif
        @foreach ($this->auditions as $audition)
            <div class="rounded-md bg-white py-4 px-8 mb-4 grid md:grid-cols-[1fr_2fr_1fr]"
                @if ($audition->is_draft) style="border: 2px dashed #f59e0b;" @endif>
                <img src="{{ config('aws.aws-urls.admin_base_url') . $audition->image }}"
                    class="h-32 rounded-md w-full object-contain"
                    @if ($audition->is_draft) style="opacity: .6;" @endif />
                <div class="py-2 px-4">
                    @if ($audition->is_draft)
                        <div style="display:inline-block;background:#fef3c7;color:#92400e;font-size:12px;font-weight:600;padding:2px 10px;border-radius:9999px;margin-bottom:6px;">
                            DRAFT · hidden from talents
                        </div>
                        <br>
                    @endif
                    <a href="/admin/auditions/{{ $audition->id }}/manage"
                        class="font-bold text-py-blue md:text-black text-sm md:text-base lg:text-lg mb-2 hover:text-py-blue">{{ $audition->title }}</a>
                    <p class="text-gray-800 text-sm">{{ $audition->description }}</p>

                    @if (auth()->user()->hasPermissionTo('view audition administrative details'))
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            @if ($audition->filming_date)
                                <div>
                                    <div class="flex flex-row items-center">
                                        <x-icons.audition class="h-5 w-5" />
                                        <p class="text-py-blue font-medium text-xs ml-1">Date</p>
                                    </div>
                                    <p class="text-gray-800 font-medium text-xs mt-2">{{ $audition->filming_date }} </p>
                                </div>
                            @endif

                            @if ($audition->applications_deadline)
                                <div>
                                    <div class="flex flex-row items-center">
                                        <x-icons.time class="h-5 w-5" />
                                        <p class="text-py-orange font-medium text-xs ml-1">Deadline</p>
                                    </div>

                                    <p class="text-gray-800 font-medium text-xs mt-2">
                                        {{ $audition->applications_deadline }}
                                    </p>
                                </div>
                            @endif

                            @if ($audition->total_budget)
                                <div>
                                    <div class="flex flex-row items-center">
                                        <x-icons.dollar class="h-5 w-5" />
                                        <p class="text-py-green font-medium text-xs ml-1">Budget</p>
                                    </div>

                                    <p class="text-gray-800 font-medium text-xs mt-2">{{ $audition->total_budget }}
                                        {{ $audition->currency }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 mt-4 md:mt-0 justify-evenly gap-y-2">
                    @if ($audition->is_draft && auth()->user()->hasPermissionTo('create audition'))
                        <button type="button"
                            wire:click="publishAudition({{ $audition->id }})"
                            wire:confirm="Publish this audition? Talents in its countries will be able to see it and will get a notification."
                            wire:loading.attr="disabled"
                            class="btn min-w-[125px] text-center flex items-center justify-center"
                            style="background:#16a34a;border-color:#16a34a;color:#fff;">
                            <x-heroicon-o-paper-airplane class="h-4 w-4 mr-2" />
                            Publish
                        </button>
                    @endif

                    @if (auth()->user()->hasPermissionTo('edit audition'))
                        <a href="/admin/auditions/{{ $audition->id }}/edit?projectId={{ $audition->project_id }}"
                            class="btn text-py-orange min-w-[125px] text-center border-py-orange block hover:bg-py-orange hover:text-white flex items-center justify-center">
                            <x-heroicon-o-pencil-square class="h-4 w-4 mr-2" />
                            Edit
                        </a>
                    @endif

                    @if (auth()->user()->hasPermissionTo('view audition administrative details'))
                        <a href="/admin/auditions/{{ $audition->id }}/view"
                            class="btn text-py-blue min-w-[125px] text-center border-py-blue block hover:bg-py-blue hover:text-white flex items-center justify-center">
                            <x-heroicon-o-eye class="h-4 w-4 mr-2" />
                            Preview
                        </a>
                    @endif

                    @if (auth()->user()->hasPermissionTo('view referrals'))
                        <a href="/admin/reward-referrals?auditionId={{ $audition->id }}&projectId={{ $audition->project_id }}"
                            class="btn text-py-purple min-w-[125px] text-center border-py-purple block hover:bg-py-purple hover:text-white flex items-center justify-center">
                            <x-heroicon-o-currency-dollar class="h-4 w-4 mr-2" />
                            Referrals
                        </a>
                    @endif

                    @if (auth()->user()->hasPermissionTo('access talent messages'))
                        <a href="{{ route('filament.admin.resources.messages.audition-message-chats', ['record' => $audition->id]) }}"
                            class="btn text-py-green min-w-[125px] text-center border-py-green block hover:bg-py-green hover:text-white flex items-center justify-center">
                            <x-heroicon-o-envelope class="h-4 w-4 mr-2" />
                            Messages
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
