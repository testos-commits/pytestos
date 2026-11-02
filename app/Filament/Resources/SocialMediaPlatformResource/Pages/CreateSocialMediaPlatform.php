<?php

namespace App\Filament\Resources\SocialMediaPlatformResource\Pages;

use App\Filament\Resources\SocialMediaPlatformResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use App\Traits\HasFilamentTranslatableFields;
class CreateSocialMediaPlatform extends CreateRecord
{   
    use HasFilamentTranslatableFields;
    protected $translatableFields = [
        'name',
    ];
    protected static string $resource = SocialMediaPlatformResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->setMutatedData($data);


        $data['slug'] = Str::slug($data['name']['en'], '_');
        return $data;
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$this->makeTranslatableField('name', TextInput::class),
            ]);
    }
}
