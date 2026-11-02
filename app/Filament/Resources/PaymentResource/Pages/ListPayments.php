<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\IconColumn;
use App\Services\PaymentsService;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Filament\Tables\Columns\ViewColumn;
use Filament\Actions;


class ListPayments extends ListRecords
{
    public $projectId;
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        parent::mount();
        $this->projectId = request()->record;
    }

    public function table(Table $table): Table
    {

        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('profile.user')->projectAccounting($this->projectId))
            ->columns([
                TextColumn::make('profile.id')->label('#'),
                TextColumn::make('profile.first_name')->label('First Name'),
                TextColumn::make('profile.last_name')->label('Last Name'),
                TextColumn::make('audition.title')->label('Audition'),
                TextColumn::make('audition.filming_budget')->label('Amount'),
                TextColumn::make('audition.currency')->label('Currency'),
                TextColumn::make('custom_amount')->label('Custom amount'),
                TextColumn::make('custom_currency')->label('Custom amount'),
                TextColumn::make('updated_at')->label('Last modified'),
                TextColumn::make('status')
                    ->label('Payment Status'),
                IconColumn::make('icon')
                    ->label('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'ready_for_collection' => 'heroicon-m-hand-thumb-up',
                        'pending' => 'heroicon-o-clock',
                        'paid' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'ready_for_collection' => 'info',
                        'pending' => 'warning',
                        'paid' => 'success',
                    }),
                ViewColumn::make('')->view('tables.columns.whatsapp-widget')->label('Contact'),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'ready_for_collection' => 'Ready for collection',
                        'paid' => 'Paid',
                    ])
            ])->bulkActions([
                BulkAction::make('make_pending')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update(['status' => 'pending']);

                        $profileIds = $records->pluck('user_profile_id')->toArray();
                        PaymentsService::timestampWallet(array_unique($profileIds));

                        return redirect()->route('filament.admin.resources.payments.index', ['record' => $this->projectId]);
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('make_ready_for_collection')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update(['status' => 'ready_for_collection']);

                        $profileIds = $records->pluck('user_profile_id')->toArray();
                        PaymentsService::timestampWallet(array_unique($profileIds));


                        return redirect()->route('filament.admin.resources.payments.index', ['record' => $this->projectId]);
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('make_paid')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update(['status' => 'paid']);

                        $profileIds = $records->pluck('user_profile_id')->toArray();
                        PaymentsService::timestampWallet(array_unique($profileIds));

                        return redirect()->route('filament.admin.resources.payments.index', ['record' => $this->projectId]);
                    })
                    ->deselectRecordsAfterCompletion(),

            ]);
    }
    // route('filament.admin.resources.messages.audition-message-chats
    public function getHeader(): ?View
    {
        return view('filament.layout.back-button', [
            'backLink' => '/admin/projects',
            'backText' => 'Back to Projects',
            'headerTitle' => 'Payments',
            'rightButtons' => [
                [
                    'text' => 'Add Payment',
                    'url' => route('filament.admin.resources.payments.create',  $this->projectId),
                ]
            ]
        ]);
    }
}
