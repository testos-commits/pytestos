<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use App\Services\PaymentsService;
use Filament\Forms\Components\Select;
use App\Jobs\NotifyAppUsers;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Grid;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $payment = PaymentsService::getById($this->record->id);

        $type = 'PAYMENT_STATUS_CHANGED';

        if ($this->record->status === 'ready_for_collection') {
            $type = 'PAYMENT_READY_FOR_PICKUP';
        } else if ($this->record->status === 'paid') {
            $type = 'PAYMENT_SETTLED';
        } else if ($this->record->status === 'payment_in_process') {
            $type = 'PAYMENT_IN_PROCESS';
        }

        NotifyAppUsers::dispatch(
            $type,
            $payment->project,
            $this->data['user_profile_id'],
            'some'
        )->onQueue('notification-chunks');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $payment = PaymentsService::getById($data['id']);

        $data['user_profile_id'] = $payment->user_profile_id;
        $data['audition'] = $payment->audition->title;
        $data['project'] = $payment->project->title;
        $data['name'] = $payment->profile->first_name . ' ' . $payment->profile->last_name;
        $data['phone'] = $payment->profile->phone ?? 'N/A';
        $data['email'] = $payment->profile->user->email;
        $data['amount'] = (string)$payment->audition->filming_budget . ' ' . $payment->audition->currency;

        $data['payment_name'] = $payment->profile->user->paymentDetails ? $payment->profile->user->paymentDetails->name : "N/A";
        $data['bank_name'] = $payment->profile->user->paymentDetails ? $payment->profile->user->paymentDetails->bank_name : "N/A";
        $data['iban'] = $payment->profile->user->paymentDetails ? $payment->profile->user->paymentDetails->iban : "N/A";
        $data['swift'] = $payment->profile->user->paymentDetails ? $payment->profile->user->paymentDetails->swift : "N/A";

        $data['custom_amount'] = $payment->custom_amount;
        $data['custom_currency'] = $payment->custom_currency;
        return $data;
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('project')->disabled(),
                TextInput::make('audition')->disabled(),
                TextInput::make('name')->disabled(),

                Grid::make([
                    "default" => 12
                ])

                    ->schema([
                        TextInput::make('phone')->disabled()->columnSpan(10),
                        ViewField::make('')
                            ->view('tables.columns.whatsapp-widget')->viewData(['size' => 10])
                            ->columnSpan(2),
                    ]),
                TextInput::make('email')->disabled(),
                TextInput::make('amount')->disabled(),

                TextInput::make('payment_name')->label('Name (for bank payment)')->disabled(),
                TextInput::make('bank_name')->disabled(),
                TextInput::make('iban')->disabled(),
                TextInput::make('swift')->disabled(),

                TextInput::make('custom_amount')->numeric(),
                Select::make('custom_currency')
                    ->options(config('currencies.currencies')),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        // 'payment_in_process' => 'Payment in process',
                        'ready_for_collection' => 'Ready for collection',
                        'paid' => 'Paid',
                    ])

            ])->columns(1);
    }
}
