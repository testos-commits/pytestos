<?php

namespace App\Filament\Resources\AuditionResource\Pages;

use App\Filament\Resources\AuditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Audition;

class ViewAudition extends ViewRecord
{
    protected static string $resource = AuditionResource::class;
    protected static string $view = 'filament.auditions.pages.view-audition';

    public function getAuditionProperty()
    {
        return Audition::with(['project.user', 'country'])->find($this->getRecord()->id);
    }


    public function getNavigationUrlsProperty()
    {
        $record = $this->getRecord();

        return [
            'edit' => '/admin/auditions/' . $record->id . '/edit?projectId=' . $record->project_id,
            'back' => '/admin/auditions?projectId=' . $record->project_id
        ];
    }

    public function getTagsProperty()
    {
        $tags = [];

        $audition = $this->getRecord();

        if ($audition->gender) {
            $tags[] = $audition->gender;
        }

        if ($audition->min_age && !$audition->max_age) {
            $tags[] = (string) $audition->min_age . ' yo';
        }

        if ($audition->max_age && !$audition->min_age) {
            $tags[] = (string) $audition->min_age . ' yo';
        }

        if ($audition->min_age && $audition->max_age) {
            $tags[] = (string) $audition->min_age . ' yo - ' . (string) $audition->max_age . ' yo';
        }

        foreach ($audition->filters as $filters) {
            if (sizeof($filters['options'])) {
                foreach ($filters['options'] as $filter) {
                    $tags[] = $filter;
                }
            }
        }

        return $tags;
    }
}
