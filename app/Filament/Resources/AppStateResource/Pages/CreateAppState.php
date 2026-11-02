<?php

namespace App\Filament\Resources\AppStateResource\Pages;

use App\Filament\Resources\AppStateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Traits\HasFilamentTranslatableFields;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;

class CreateAppState extends CreateRecord
{
    use HasFilamentTranslatableFields;

    protected static string $resource = AppStateResource::class;

    protected $translatableFields = [
        'popup_title', 'popup_message'
    ];
    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_maintenance')
                    ->required(),
                Forms\Components\TextInput::make('current_version')
                    ->maxLength(255),
                ...$this->makeTranslatableField('popup_title', TextInput::class, notRequired: true),
                ...$this->makeTranslatableField('popup_message', TextInput::class, notRequired: true),
                Forms\Components\Toggle::make('show_button')
                    ->required(),
            ]);
    }
}
