<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RewardReferralResource\Pages;
use App\Filament\Resources\RewardReferralResource\RelationManagers;
use App\Models\RewardReferral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class RewardReferralResource extends Resource
{
    protected static ?string $model = RewardReferral::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('referrer_id')
                    ->disabled()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('referred_id')
                    ->disabled()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('audition_id')
                    ->disabled()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('reward')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('audition', function (Builder $query) {
                    $query->whereHas('project', function (Builder $query) {
                        $query->authorizedProjects();
                    });
                });
            })
         
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRewardReferrals::route('/'),
            'create' => Pages\CreateRewardReferral::route('/create'),
            'edit' => Pages\EditRewardReferral::route('/{record}/edit'),
        ];
    }
}
