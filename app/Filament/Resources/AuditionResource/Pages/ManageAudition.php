<?php

namespace App\Filament\Resources\AuditionResource\Pages;

use App\Filament\Resources\AuditionResource;
use App\Jobs\ProcessProfilesDownload;
use Filament\Resources\Pages\Page;
use App\Services\AuditionService;
use Illuminate\Contracts\View\View;
use App\Traits\CanManageApplications;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class ManageAudition extends Page
{
    use CanManageApplications;

    protected static string $resource = AuditionResource::class;
    protected static string $view = 'filament.auditions.pages.manage-audition';

    public $showFilters = false;

    public $filtersData = ['data' => []];
    public $searchQuery = '';

    public $auditionId;


    public function mount(int | string $record): void
    {
        static::authorizeResourceAccess();
        $this->auditionId = $record;
    }

    #[Computed]
    public function audition()
    {
        return AuditionService::getForPriviledgedUser($this->auditionId, $this->searchQuery, $this->filtersData['data']);
    }

    #[On('hide-filters')]
    public function hideFilters(): void
    {
        $this->showFilters = false;
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



    public function downloadSelectedProfiles()
    {
        if (count($this->selectedApplicants) === 0) {
            $this->notify('Please select at least one applicant to download.', 'danger');
            return;
        }

        ProcessProfilesDownload::dispatch($this->selectedApplicants, $this->audition, auth()->user())->onQueue('download-requests');
        $this->notify('Download request has been queued. You will be notified when it is ready.', 'success');
    }

    public function getHeader(): ?View
    {
        return view('filament.layout.manage-audition-header', [
            'backLink' => '/admin/auditions?projectId=' . $this->audition->project_id,
            'backText' => 'Back to Auditions',
            'title' => 'Audition | ' . $this->audition->project->title,
            'subtitle' => $this->audition->title,
            'buttonText' => 'Create Project',
            'buttonLink' => '/admin/projects/create',
        ]);
    }
}
