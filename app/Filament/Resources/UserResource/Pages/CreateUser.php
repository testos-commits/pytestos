<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use App\Models\User;


class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    public function getFields()
    {
        $fields = [];
        $user = User::find(auth()->id());

        $fields = [
            TextInput::make('name')->required(),
            TextInput::make('email')->required(),
            TextInput::make('password')->required(),
            DateTimePicker::make('email_verified_at'),
        ];
        if ($user->hasRole('super admin')) {
            $fields[] = Select::make('roles')->relationship('roles', 'name');
        }

        return $fields;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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
            ->schema($this->getFields())->columns(1);
    }
}
