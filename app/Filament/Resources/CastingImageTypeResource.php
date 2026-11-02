<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CastingImageTypeResource\Pages;
use App\Filament\Resources\CastingImageTypeResource\RelationManagers;
use App\Models\CastingImageType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;




class CastingImageTypeResource extends Resource
{   
    use Translatable;
    protected static ?string $model = CastingImageType::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            
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
            'index' => Pages\ListCastingImageTypes::route('/'),
            'create' => Pages\CreateCastingImageType::route('/create'),
            'edit' => Pages\EditCastingImageType::route('/{record}/edit'),
        ];
    }
}
