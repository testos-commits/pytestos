<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\AuditionApplication;
use Filament\Resources\Pages\Page;
use App\Services\ApplicationService;
use Illuminate\Contracts\View\View;
use App\Services\AuditionService;

class ManageApplication extends Page
{
    protected static string $resource = ProfileResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    protected static string $view = 'filament.profiles.pages.manage-application';
    public $tabs = [
        'about' => 'About',
        'information-skills' => 'Information & Skills',
        'introduction-video' => 'Introduction Video',
        'photos' => 'Photos',
    ];




    public $selectedImage = null;

    public $activeTab = 'About';
    public $showImageViewer = false;
    public $application;
    public $applicationId;
    public $nextApplication = null;
    public $previousApplication = null;

    public $pool;

    public function mount(int | string $record): void
    {
        static::authorizeResourceAccess();
        $this->applicationId = $record;
        $this->application = ApplicationService::getProfileApplication($this->applicationId);
        $this->pool = request()->get('pool') ?? false;

        if(auth()->user()->hasPermissionTo('view talent previous commercials')){
            $this->tabs['previous-commercials'] = 'Previous Commercials';
        }
    }

    public function showImage($image)
    {
        $this->selectedImage = $image;
        $this->showImageViewer = true;
    }

    public function getApplicationProperty() {
        return AuditionApplication::find($this->applicationId);
    }

    public function hydrate()
    {
        $this->application = ApplicationService::getProfileApplication($this->applicationId);
    }

    public function changeStatus($action)
    {
        ApplicationService::changeApplicationStatus($this->application->audition, $this->application->profile->id, $action);
        $this->application = ApplicationService::getProfileApplication($this->applicationId);
        // $this->refreshComponent();
    }


    protected function refreshComponent()
    {
        $this->dispatch('$refresh');
    }

    public function getAuditionProperty()
    {
        return AuditionService::getForPriviledgedUser($this->application->audition->id);
    }




    public function getHeader(): ?View
    {
        $headerData = [];

        if ($this->pool) {
            $poolResults = $this->audition->{$this->pool}->pluck('pivot')->toArray();

            foreach ($poolResults as $key => $value) {
                if ($value['id'] === $this->application->id) {

                    if (isset($poolResults[$key - 1])) {
                        $this->previousApplication = $poolResults[$key - 1]['id'];
                    }

                    if (isset($poolResults[$key + 1])) {
                        $this->nextApplication = $poolResults[$key + 1]['id'];
                    }
                    break;
                }
            }


            if ($this->previousApplication) {
                $headerData['previousCandidateUrl'] = '/admin/profiles/application/' . $this->previousApplication . '?pool=' . $this->pool;
            }

            if ($this->nextApplication) {
                $headerData['nextCandidateUrl'] =  '/admin/profiles/application/' . $this->nextApplication . '?pool=' . $this->pool;
            }
        }

        return view('filament.profiles.components.header', [
            'backLink' => '/admin/auditions/' . $this->application->audition->id . '/manage',
            'backText' => 'Back to applications',
            'title' => 'Applicant',
            'subtitle' => $this->application->audition->title,
            'applicant_id' => $this->application->profile->id,
            'buttonText' => 'Create Project',
            'buttonLink' => '/admin/projects/create',
            ...$headerData
        ]);
    }
}
