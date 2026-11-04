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
use App\Models\Audition;
use Filament\Notifications\Notification;

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

    /** Publish a draft: makes it visible to talents and sends the new-audition notification. */
    public function publishAudition(int $auditionId): void
    {
        abort_unless(auth()->user()->hasPermissionTo('create audition'), 403);

        $audition = Audition::where('project_id', $this->projectId)->findOrFail($auditionId);

        if (AuditionService::publish($audition)) {
            Notification::make()
                ->title('Published. Talents can now see and apply to "' . $audition->title . '".')
                ->success()
                ->send();
        }

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
