<?php

namespace App\Filament\Resources\AuditionResource\Pages;

use App\Filament\Resources\AuditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use App\Services\AuditionService;
use App\Services\ProjectService;
use Exception;
use Filament\Panel;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use App\Interfaces\Searchable;

class ListAuditions extends ListRecords implements Searchable
{
    protected static string $resource = AuditionResource::class;
    protected static string $view = 'filament.auditions.pages.list-auditions';

    public $searchQuery = '';
    public $auditions;
    public $projectId;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function updatedSearchQuery(string $value): void
    {
        $this->auditions = AuditionService::getProjectAuditions($this->projectId, $this->searchQuery);
    }

    public function mount(): void
    {
        if (!request()->has('projectId')) {
            throw new Exception('Project id is required');
        }
        
        $project = ProjectService::getById(request()->get('projectId'));


        if (!$project->authorize()) {
            throw new Exception('You are not authorized to view this page');
        }

        $this->projectId = request()->projectId;
        $this->auditions = AuditionService::getProjectAuditions(request()->projectId);
    }

    /** Customize the header */
    public function getHeader(): ?View
    {
        return view('components.layouts.header', [
            'backLink' => '/admin/projects',
            'backText' => 'Back to projects',
            'title' => 'Auditions',
            'buttonText' => auth()->user()->hasPermissionTo('create audition') ? 'Create Audition' : null,
            'buttonLink' => AuditionResource::getUrl('create', ['projectId' => $this->projectId]),
            'placeholder' => 'Search Auditions',
            'subtitle' => null
        ]);
    }
}
