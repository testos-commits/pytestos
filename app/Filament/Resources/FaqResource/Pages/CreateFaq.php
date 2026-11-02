<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Traits\HasFilamentTranslatableFields;
class CreateFaq extends CreateRecord
{
    use HasFilamentTranslatableFields;
    protected static string $resource = FaqResource::class;

    protected $translatableFields = [
        'question',
        'answer',
    ];
    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$this->makeTranslatableField('question', TextInput::class),
                ...$this->makeTranslatableField('answer', Textarea::class),
            ]);
    }
}
