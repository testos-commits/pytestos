<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use App\Services\NewsService;
use App\Models\News;
use App\Traits\HasFilamentTranslatableFields;

class EditNews extends EditRecord
{
    use HasFilamentTranslatableFields;
    protected static string $resource = NewsResource::class;

    protected $translatableFields = [
        'title',
        'content',
    ];
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getRecord(): Model
    {

        return News::with('countries')->find($this->record->id);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $data = $this->getMutatedData($data);
        $data['countries'] = $this->record->countries->pluck('id')->toArray();
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['id'] = $record->id;
        return NewsService::update($data);
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$this->makeTranslatableField('title', TextInput::class),
                ...$this->makeTranslatableField('content', RichEditor::class),
                FileUpload::make('image')
                    ->image()
                    ->disk('s3_admin')
                    ->directory(config('aws-bucket-directories.news'))
                    ->required(),
                Select::make('countries')->options(
                    \App\Models\Country::where('is_active', 1)->get()->pluck('name', 'id')
                )->required()->multiple(),
            ]);
    }
}
