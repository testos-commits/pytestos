<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Filters\Filter;

use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Users';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function getConfiguration()
    {
        $user = User::find(auth()->id());

        $config = [
            'filters' => [],
            'columns' => [
                TextColumn::make('email')->searchable(),
                TextColumn::make('referral_code')->searchable(),
            ],
            'fields' => [],
        ];
        if ($user->hasRole('super admin')) {
            $config['filters'] = [
                Filter::make('Admins')
                    ->query(fn(Builder $query): Builder => $query->orwhere(function ($q) {
                        $q->orWhereHas('roles', function ($q2) {
                            $q2->where('name', 'super admin');
                        });
                    })),
                Filter::make('Partners')
                    ->query(fn(Builder $query): Builder => $query->orWhere(function ($q) {
                        $q->orWhereHas('roles', function ($q2) {
                            $q2->where('name', 'partner');
                        });
                    })),
                Filter::make('Directors')
                    ->query(fn(Builder $query): Builder => $query->orWhere(function ($q) {
                        $q->orWhereHas('roles', function ($q2) {
                            $q2->where('name', 'director');
                        });
                    })),
                Filter::make('Super Directors')
                    ->query(fn(Builder $query): Builder => $query->orWhere(function ($q) {
                        $q->orWhereHas('roles', function ($q2) {
                            $q2->where('name', 'super director');
                        });
                    })),
            ];

            $config['columns'] = [
                TextColumn::make('email')->searchable(),
                TextColumn::make('referral_code')->searchable(),
                TextColumn::make('roles')->formatStateUsing(
                    fn($state, $record) => $record->roles->pluck('name')->join(', ')
                ),
            ];
        }

        return $config;
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getConfiguration()['columns'])
            ->filters(self::getConfiguration()['filters'])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = User::find(auth()->id());
                if ($user->hasRole('country admin')) {
                    $query->whereHas('profiles', function ($query) {
                        $query->where('country_id', auth()->user()->countries->pluck('id'));
                    });
                }
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
