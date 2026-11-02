<?php

namespace App\Filament\Resources\AdminMessageResource\Pages;

use App\Filament\Resources\AdminMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminMessage extends EditRecord
{
    protected static string $resource = AdminMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
