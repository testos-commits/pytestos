<?php

namespace App\Filament\Resources\UserDownloadRequestResource\Pages;

use App\Filament\Resources\UserDownloadRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserDownloadRequest extends EditRecord
{
    protected static string $resource = UserDownloadRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
