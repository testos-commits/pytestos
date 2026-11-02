<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Str;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Str::slug($data['name'], '_');

        foreach ($data['options'] as $key => $option) {
            $data['options'][Str::slug($option['en'], '_')] = $option;
            unset($data['options'][$key]);
        }

        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('name_ar')->required(),
                Repeater::make('options')
                    ->schema([
                        TextInput::make('en')->required(),
                        TextInput::make('ar')->required(),
                        TextInput::make('el')->required(),
                    ])
                    ->columns(2)
            ])
            ->columns(1);
    }
}
