<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialMediaPlatformResource\Pages;
use App\Filament\Resources\SocialMediaPlatformResource\RelationManagers;
use App\Models\SocialMediaPlatform;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;

class SocialMediaPlatformResource extends Resource
{
    use Translatable;
    protected static ?string $model = SocialMediaPlatform::class;

    protected static ?string $navigationIcon = 'ri-instagram-line';

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
            'index' => Pages\ListSocialMediaPlatforms::route('/'),
            'create' => Pages\CreateSocialMediaPlatform::route('/create'),
            'edit' => Pages\EditSocialMediaPlatform::route('/{record}/edit'),
        ];
    }
}
