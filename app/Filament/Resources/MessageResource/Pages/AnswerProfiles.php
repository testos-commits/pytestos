<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Resources\Pages\Page;
use App\Traits\CanManageApplications;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use App\Services\AuditionService;
use Livewire\Attributes\On;

class AnswerProfiles extends Page
{
    use CanManageApplications;

    protected static string $resource = MessageResource::class;
    protected static string $view = 'filament.auditions.pages.manage-audition';

    public $filtersData = ['data' => []];
    public $showFilters = false;

    public $auditionId;
    public $answerId;
    public $searchQuery = '';

    public function mount($auditionId, $answerId): void
    {
        static::authorizeResourceAccess();

        $this->auditionId = $auditionId;
        $this->answerId = $answerId;
    }

    #[On('hide-filters')]
    public function hideFilters(): void
    {
        $this->showFilters = false;
    }

    #[Computed] 
    public function audition()
    {
        return AuditionService::getForPriviledgedUserByAnswerId(
            auditionId: $this->auditionId,
            answerId: $this->answerId,
            searchQuery: $this->searchQuery,
            filters: $this->filtersData['data']
        );
    }

    #[On('filters-updated')]
    public function updateFilters($data): void
    {
        $this->filtersData['data'] = $data;
        $this->showFilters = false;
    }

    #[On('reset-filters')]
    public function resetFilters(): void
    {
        $this->filtersData['data'] = [];
        unset($this->audition);
    }

    public function getHeader(): ?View
    {
        return view('filament.layout.manage-audition-header', [
            'backLink' => '/admin/messages/' . $this->audition->id,
            'backText' => 'Back to Auditions',
            'title' => 'Audition',
            'subtitle' => $this->audition->title,
            'buttonText' => 'Create Project',
            'buttonLink' => '/admin/projects/create',
        ]);
    }
}
