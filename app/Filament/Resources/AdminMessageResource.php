<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminMessageResource\Pages;
use App\Filament\Resources\AdminMessageResource\RelationManagers;
use App\Models\AdminMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class AdminMessageResource extends Resource
{
    protected static ?string $model = AdminMessage::class;

    protected static ?string $navigationIcon = 'ri-message-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Textarea::make('message')->required(),
                // Textarea::make('message_ar')->required(),
                // Select::make('country_id')->options(\App\Models\Country::where('is_active',1)->get()->pluck('name', 'id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('message'),
                // TextColumn::make('message_ar'),
                TextColumn::make('country.name'),
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
            'index' => Pages\ListAdminMessages::route('/'),
            'create' => Pages\CreateAdminMessage::route('/create'),
            'edit' => Pages\EditAdminMessage::route('/{record}/edit'),
        ];
    }
}
