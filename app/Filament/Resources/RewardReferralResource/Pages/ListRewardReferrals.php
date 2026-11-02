<?php

namespace App\Filament\Resources\RewardReferralResource\Pages;

use App\Filament\Resources\RewardReferralResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\View\View;

class ListRewardReferrals extends ListRecords
{
    public $auditionId;

    protected static string $resource = RewardReferralResource::class;
    public function mount(): void
    {
        parent::mount();
        $this->auditionId = request()->auditionId;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getHeader(): ?View
    {
        return view('filament.layout.back-button', [
            'backLink' => '/admin/auditions?projectId=' . request()->projectId,
            'backText' => 'Back to Auditions',
            'headerTitle' => 'Reward Referrals'
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referrer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referred_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('audition.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reward')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->where('audition_id', $this->auditionId))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
