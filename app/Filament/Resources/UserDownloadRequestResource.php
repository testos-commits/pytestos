<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDownloadRequestResource\Pages;
use App\Filament\Resources\UserDownloadRequestResource\RelationManagers;
use App\Models\UserDownloadRequest;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserDownloadRequestResource extends Resource
{
    protected static ?string $model = UserDownloadRequest::class;

    protected static ?string $navigationIcon = 'ri-download-cloud-2-fill';


    public static function canCreate(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('audition_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('zip_path')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('hash')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_downloaded')
                    ->required(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            $query->latest();
            
            // super admins don't need to be restricted
            if(auth()->user()->hasRole('super admin')){
                return $query;
            }
            return $query->where('user_id', auth()->user()->id);
        })
            ->columns([
                Tables\Columns\TextColumn::make('audition.title')
                    ->numeric()
                    ->sortable(),


                Tables\Columns\IconColumn::make('is_downloaded')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->requiresConfirmation()
                    ->url(fn (UserDownloadRequest $record): string => route('download', ['hash' => $record->hash]))
                // ->message('Are you sure you want to download this?')
                // ->confirmToProceed('Are you sure you want to download this?')
                // ->danger()
                // ->modal('download'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->query(function (Builder $query) {
                return UserDownloadRequest::where('user_id', auth()->user()->id);
            });;
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
            'index' => Pages\ListUserDownloadRequests::route('/'),
            // 'create' => Pages\CreateUserDownloadRequest::route('/create'),
            // 'edit' => Pages\EditUserDownloadRequest::route('/{record}/edit'),
        ];
    }
}
