<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\DateTimePicker;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {   
        if (isset($data['password']) && $data['password'] !== null) {
            $data['password'] = bcrypt($data['password']);
            return $data;
        } else {
            unset($data['password']);
            return $data;
        }
    }


    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')->required(),
                TextInput::make('password'),
                TextInput::make('name')->required(),
                DateTimePicker::make('email_verified_at'),
                Select::make('roles')->relationship('roles', 'name')->hidden(!auth()->user()->hasRole('super admin')),
                // Checkbox::make('is_admin'),
                // Checkbox::make('is_director'),
                // Checkbox::make('is_partner'),
                Checkbox::make('disabled')
                    ->label('Disabled')
                    ->reactive()
                    ->helperText('Disables user access  to the website'),
            ])->columns(1);
    }
    
}
