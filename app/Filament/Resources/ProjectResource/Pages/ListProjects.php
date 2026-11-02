<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use App\Services\ProjectService;
use Filament\Panel;
use Illuminate\Support\Facades\Blade;
use App\Interfaces\Searchable;
class ListProjects extends ListRecords implements Searchable
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.projects.pages.list-projects';
    public $searchQuery = '';

    public $projects;

    public function mount(): void
    {
        parent::mount();
        $this->projects = $this->getProjects();

    }
    /** Customize the header */
    public function getHeader(): ?View
    {
        return view('filament.layout.list-projects-header', [
            'title' => 'Projects',
            'buttonText' => auth()->user()->hasPermissionTo('create project') ? 'Create Project' : null,
            'buttonLink' => '/admin/projects/create',
            'placeholder' => 'Search Projects',
            'subtitle' => null
        ]);
    }

    public function updatedSearchQuery(string $value): void
    {
        $this->projects = $this->getProjects();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getProjects()
    {
        $projects = ProjectService::getAdminProjects($this->searchQuery);

        $projects->map(function ($project) {
            $project->accepted_count = $project->auditions->sum('accepted_count');
            $project->shortlisted_count = $project->auditions->sum('shortlisted_count');
            $project->applied_count = $project->auditions->sum('applied_count');
        });

        return $projects;
    }
}
