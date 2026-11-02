<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use Filament\Resources\Pages\Page;
use App\Services\ProfileService;
use App\Interfaces\Searchable;
use Illuminate\Contracts\View\View;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On; 

class AdminListProfiles extends Page 
{
    use WithPagination;

    protected static string $resource = ProfileResource::class;


    protected static string $view = 'filament.profiles.pages.admin-list-profiles';

    public int | string $perPage = 12;


    public $showFilters = false;

    public $filtersData = [ 'data' => [] ];

    public $links;
    public $searchQuery = '';

    public function mount(): void
    {

        static::authorizeResourceAccess();
    }



    public function getHeader(): ?View
    {
        return view('filament.layout.admin-list-profiles-header', [
            'title' => 'Profiles',
            'buttonText' => 'Create Profile',
            'buttonLink' => '/admin/profiles/create',
            'placeholder' => 'Search Profiles',
            'subtitle' => null
        ]);
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
        unset($this->profiles);
    }


    #[Computed] 
    public function profiles() {
        return ProfileService::getAllProfiles($this->searchQuery, $this->filtersData['data'], $this->perPage);
    }
    
}
