<?php

namespace App\Filament\Resources\AdminCountryResource\Pages;

use App\Filament\Resources\AdminCountryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminCountry extends EditRecord
{
    protected static string $resource = AdminCountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
