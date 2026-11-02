<?php

namespace App\Filament\Resources\SocialMediaPlatformResource\Pages;

use App\Filament\Resources\SocialMediaPlatformResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use App\Traits\HasFilamentTranslatableFields;

class EditSocialMediaPlatform extends EditRecord
{
    use HasFilamentTranslatableFields;
    protected static string $resource = SocialMediaPlatformResource::class;

    protected $translatableFields = [
        'name',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
