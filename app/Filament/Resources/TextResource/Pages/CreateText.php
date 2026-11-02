<?php

namespace App\Filament\Resources\TextResource\Pages;

use App\Filament\Resources\TextResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;
// use App\Traits\HasFilamentTranslatableFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;


class CreateText extends CreateRecord
{

    use CreateRecord\Concerns\Translatable;

    protected static string $resource = TextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->label('Title')->required(),
                TextInput::make('section')->label('Section (Never change or delete this afrer it is created )')->required(),
                RichEditor::make('content')->label('Content')->required(),
            ])->columns(1);
    }
}
