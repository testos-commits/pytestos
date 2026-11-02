<?php

namespace App\Filament\Resources\TextResource\Pages;

use App\Filament\Resources\TextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
class EditText extends EditRecord
{
    protected static string $resource = TextResource::class;
    use EditRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
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
