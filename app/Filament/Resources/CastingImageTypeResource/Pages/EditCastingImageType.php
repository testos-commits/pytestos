<?php

namespace App\Filament\Resources\CastingImageTypeResource\Pages;

use App\Filament\Resources\CastingImageTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use App\Traits\HasFilamentTranslatableFields;

class EditCastingImageType extends EditRecord
{
    use HasFilamentTranslatableFields;

    protected $translatableFields = [
        'name',
        'description',
    ];
    protected static string $resource = CastingImageTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$this->makeTranslatableField('name', TextInput::class),
                ...$this->makeTranslatableField('description', Textarea::class),
                FileUpload::make('image')
                    ->disk('s3_admin')
                    ->directory(config('aws-bucket-directories.casting_image_types'))
                    ->image()
                    ->required(),
            ]);
    }
}
