<?php

namespace App\Filament\Resources\AdminMessageResource\Pages;

use App\Filament\Resources\AdminMessageResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use App\Services\NotificationsService;
use App\Traits\HasFilamentTranslatableFields;
use App\Jobs\NotifyAppUsers;

class CreateAdminMessage extends CreateRecord
{
    use HasFilamentTranslatableFields;
    protected $translatableFields = ['message'];

    protected static string $resource = AdminMessageResource::class;


    protected function afterCreate(): void
    {
        if ($this->data['country_id']) {
            NotifyAppUsers::dispatch(
                'NEW_ADMIN_MESSAGE',
                $this->getRecord(),
                $this->data['country_id'],
                'countries'
            )->onQueue('notification-chunks');
        } else {
            NotifyAppUsers::dispatch(
                'NEW_ADMIN_MESSAGE',
                $this->getRecord(),
                false,
                'all'
            )->onQueue('notification-chunks');
        }
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$this->makeTranslatableField('message', Textarea::class),
                Select::make('country_id')->options(\App\Models\Country::where('is_active', 1)->get()->pluck('name', 'id'))
            ]);
    }
}
