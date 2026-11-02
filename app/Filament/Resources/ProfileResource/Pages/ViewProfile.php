<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Services\ProfileService;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
class ViewProfile extends ViewRecord
{

    protected static string $resource = ProfileResource::class;
    protected static string $view = 'filament.profiles.pages.view-profile';
    public $profile;

    public $tabs = [
        'about' => 'About',
        'information-skills' => 'Information & Skills',
        'introduction-video' => 'Introduction Video',
        'photos' => 'Photos',
        'previous-commercials' => 'Previous Commercials',
        'network' => 'Network',
    ];

    public $showImageViewer = false;
    public $selectedImage = null;



    public function mount(int | string $record): void
    {
        $this->profile = ProfileService::getProfileForAdmin(profileId: $record);

        $this->record = $this->profile;

        if(auth()->user()->hasRole(['super admin', 'country admin'])) {
            $this->tabs['bank_details'] ='Bank Details';
        }
    }


    public function showImage($image)
    {
        $this->selectedImage = $image;
        $this->showImageViewer = true;
    }
}
