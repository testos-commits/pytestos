<?php

namespace App\Filament\Resources\DirectorProjectResource\Pages;

use App\Filament\Resources\DirectorProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDirectorProject extends EditRecord
{
    protected static string $resource = DirectorProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
