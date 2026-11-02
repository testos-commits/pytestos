<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;
use App\Traits\HasFilamentTranslatableFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;
    use HasFilamentTranslatableFields;
    protected $translatableFields = [
        'name'
    ];
    public  function form(Form $form): Form
    {
        $bucket_keys = [];
        foreach (array_keys(config('aws-buckets')) as $key) {
            $bucket_keys[$key] = $key;
        }

        return $form
            ->schema([
                ...$this->makeTranslatableField('name', TextInput::class),
                TextInput::make('country_code')->required(),
                Select::make('aws_bucket')->options($bucket_keys)->required(),
                Checkbox::make('is_active'),
            ]);
    }
}
