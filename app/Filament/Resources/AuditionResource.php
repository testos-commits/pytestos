<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditionResource\Pages;
use App\Filament\Resources\AuditionResource\RelationManagers;
use App\Models\Audition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;
class AuditionResource extends Resource
{   
    use Translatable;
    protected static ?string $model = Audition::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListAuditions::route('/'),
            'create' => Pages\CreateAudition::route('/create'),
            'edit' => Pages\EditAudition::route('/{record}/edit'),
            'view' => Pages\ViewAudition::route('/{record}/view'),
            'manage' => Pages\ManageAudition::route('/{record}/manage')
        ];
    }
}
