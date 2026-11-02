<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use App\Services\AuditionService;
use App\Interfaces\Searchable;

class ListAuditionMessages extends Page implements Searchable
{
    protected static string $resource = MessageResource::class;

    protected static string $view = 'filament.messages.pages.list-audition-messages';
    public $audition;
    public $auditionId;
    public $searchQuery = '';

    public function mount(int | string $record): void
    {   
        static::authorizeResourceAccess();
        $this->auditionId = $record;
        $this->audition = AuditionService::getAuditionWithMessages($record);
    }

    public function updatedSearchQuery(string $value): void
    {

        $this->audition = AuditionService::getAuditionWithMessages($this->auditionId, $this->searchQuery);
    }


    public function getHeader(): View
    {
        return view('components.layouts.header', [
            'backLink' => MessageResource::getUrl('audition-message-chats', ['record' => $this->auditionId]),
            'backText' => 'Back to Chats',
            'title' => $this->audition->title,
            'buttonText' => 'Create Message',
            'buttonLink' => route('filament.admin.resources.messages.create', ['auditionId' => $this->auditionId]),
            'placeholder' => 'Search Messages',
            'subtitle' => null
        ]);
    }
}
