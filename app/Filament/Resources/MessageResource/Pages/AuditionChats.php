<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\AuditionResource;
use App\Filament\Resources\MessageResource;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use App\Services\MessageService;
use Livewire\Attributes\Computed;

class AuditionChats extends Page
{
    protected static string $resource = MessageResource::class;

    protected static string $view = 'filament.messages.pages.audition-chats';

    public  $activeProfile;
    public $auditionId;

    public function mount(int | string $record): void
    {
        static::authorizeResourceAccess();
        $this->auditionId = $record;
        $this->activeProfile = $this->audition->applications?->first()?->profile ?? null;


        if ($this->activeProfile) {
            MessageService::markReadByAdmin($this->audition->applications->first());
        }
    }


    #[Computed]
    public function audition()
    {
        return MessageService::getAdminAuditionChats($this->auditionId);
    }


    public function getHeader(): View
    {
        return view('filament.messages.components.audition-chats-header', [
            'backText' => 'Back to Audition',
            'backLink' => AuditionResource::getUrl('manage', [
                'record' => $this->audition->id
            ]),
            'title' => $this->audition->title,
            'buttonText' => 'Create Message',
            'buttonLink' => MessageResource::getUrl('create', [
                'auditionId' => $this->audition->id
            ]),
            'placeholder' => 'Search Messages',
            'subtitle' => null
        ]);
    }

    public function setActiveProfile($applicationId)
    {
        $application = MessageService::getAdminAuditionApplicationChat($this->audition->id, $applicationId);

        $this->activeProfile = $application->profile;

        MessageService::markReadByAdmin($application);
    }
}
