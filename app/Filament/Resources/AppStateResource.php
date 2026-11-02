<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppStateResource\Pages;
use App\Filament\Resources\AppStateResource\RelationManagers;
use App\Models\AppState;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;


class AppStateResource extends Resource
{
    use Translatable;
    protected static ?string $model = AppState::class;

    protected static ?string $navigationIcon = 'ri-settings-3-fill';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Toggle::make('is_maintenance')
                //     ->required(),
                // Forms\Components\TextInput::make('current_version')->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('popup_title'),
                // Forms\Components\TextInput::make('popup_message'),
                // Forms\Components\Toggle::make('show_button')
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_maintenance')
                    ->boolean(),
                Tables\Columns\TextColumn::make('current_version')
                    ->searchable(),
                Tables\Columns\IconColumn::make('show_button')
                    ->boolean(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListAppStates::route('/'),
            'create' => Pages\CreateAppState::route('/create'),
            'edit' => Pages\EditAppState::route('/{record}/edit'),
        ];
    }
}
