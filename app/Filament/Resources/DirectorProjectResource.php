<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorProjectResource\Pages;
use App\Filament\Resources\DirectorProjectResource\RelationManagers;
use App\Models\DirectorProject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class DirectorProjectResource extends Resource
{
    protected static ?string $model = DirectorProject::class;

    protected static ?string $navigationIcon = 'monoicon-user-add';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('director_id')
                    ->required()
                    ->options(
                        \App\Models\User::whereHas('roles', function ($query){
                            return $query->where('name', 'director')->orWhere('name', 'super director');
                        })->get()
                            ->mapWithKeys(function ($director) {
                                return [$director->id => $director->name];
                            })
                    ),
                Select::make('project_id')
                    ->required()
                    ->options(
                        \App\Models\Project::authorizedProjects()
                            ->orderBy('id', 'desc')
                            ->get()
                            ->mapWithKeys(function ($project) {
                                return [$project->id => '#' . $project->id . ' ' . $project->title];
                            })
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('project', function (Builder $query) {
                    $query->authorizedProjects();
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('director.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListDirectorProjects::route('/'),
            'create' => Pages\CreateDirectorProject::route('/create'),
            'edit' => Pages\EditDirectorProject::route('/{record}/edit'),
        ];
    }
}
