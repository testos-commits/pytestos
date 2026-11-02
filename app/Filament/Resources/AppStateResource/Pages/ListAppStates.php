<?php

namespace App\Filament\Resources\AppStateResource\Pages;

use App\Filament\Resources\AppStateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppStates extends ListRecords
{
    protected static string $resource = AppStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
