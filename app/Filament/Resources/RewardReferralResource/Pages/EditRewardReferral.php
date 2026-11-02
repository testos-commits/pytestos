<?php

namespace App\Filament\Resources\RewardReferralResource\Pages;

use App\Filament\Resources\RewardReferralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRewardReferral extends EditRecord
{
    protected static string $resource = RewardReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
