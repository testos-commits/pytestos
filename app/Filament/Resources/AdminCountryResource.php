<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminCountryResource\Pages;
use App\Filament\Resources\AdminCountryResource\RelationManagers;
use App\Models\AdminCountry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class AdminCountryResource extends Resource
{
    protected static ?string $model = AdminCountry::class;

    protected static ?string $navigationIcon = 'heroicon-s-globe-americas';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship(name: 'admin', titleAttribute: 'email'),
                Select::make('country_id')->options(
                    \App\Models\Country::active()->get()->pluck('name', 'id')
                )
                    ->required()
                    ->label('Country'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin.email')->label('admin'),
                TextColumn::make('country.name')->label('Country'),
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
            'index' => Pages\ListAdminCountries::route('/'),
            'create' => Pages\CreateAdminCountry::route('/create'),
            'edit' => Pages\EditAdminCountry::route('/{record}/edit'),
        ];
    }
}
