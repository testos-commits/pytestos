<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;



class EditProject extends EditRecord
{

    protected static string $resource = ProjectResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        // Add your custom query logic here
        return static::getResource()::getEloquentQuery()
            ->where('id', $key)
            ->withTrashed()
            ->firstOrFail();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotificationTitle('The project has been successfully disabled. It won\'t show to users on the app anymore.')
                ->successRedirectUrl(null),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('countries')->multiple()
                    ->relationship(titleAttribute: 'name')
                    ->options(
                        \App\Models\Country::active()->authorized()->get()->pluck('name', 'id')
                    ),
            ]);
    }
}
