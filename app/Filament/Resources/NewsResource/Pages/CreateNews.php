<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use App\Services\NotificationsService;
use Illuminate\Database\Eloquent\Model;
use App\Services\NewsService;
use App\Traits\HasFilamentTranslatableFields;
use App\Jobs\NotifyAppUsers;

class CreateNews extends CreateRecord
{
    use HasFilamentTranslatableFields;
    protected static string $resource = NewsResource::class;

    protected $translatableFields = [
        'title',
        'content',
    ];

    protected function handleRecordCreation(array $data): Model
    {
        return NewsService::create($data);
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

    protected function afterCreate(): void
    {
        NotifyAppUsers::dispatch(
            'NEW_ARTICLE',
            $this->getRecord(),
            $this->data['countries'],
            'countries'
        )->onQueue('notification-chunks');
        // $pns = new NotificationsService('NEW_ARTICLE', $this->getRecord());
        // $pns->toCountries($this->data['countries'])
        //     ->processChunks();
    }
}
