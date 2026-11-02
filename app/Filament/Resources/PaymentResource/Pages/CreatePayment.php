<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PaymentsService;
use App\Jobs\NotifyAppUsers;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use App\Models\Project;
use Filament\Forms\Set;
use App\Models\Profile;
use Filament\Forms\Components\Hidden;

class CreatePayment extends CreateRecord
{

    protected static string $resource = PaymentResource::class;
    public $auditions;
    public $project;
    public $projectId;
    public function mount(): void
    {
        $this->projectId = request()->projectId;

        $this->project =  Project::with('auditions')->find($this->projectId);
        $this->auditions = $this->project->auditions->pluck('title', 'id');

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['user_profile_id'] = $data['profile'];
        return $data;
    }



    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('project_id')
                    ->default($this->projectId),
                TextInput::make('project')
                    ->disabled()
                    ->default($this->project->title),
                Select::make('audition_id')
                    ->afterStateUpdated(function (Set $set, $state) {
                        $audition = $this->project->auditions->where('id', $state)->first();
                        $set('amount', (string)$audition->filming_budget . ' ' . $audition->currency);
                    })
                    ->live()
                    ->options($this->auditions)
                    ->required(),

                TextInput::make('amount')
                    ->disabled(),
                Select::make('profile')
                    ->label('Talent')
                    ->getSearchResultsUsing(function (string $search) {
                        $results = Profile::whereHas('user', function ($query) use ($search) {
                            $query->where('email', 'like', "%{$search}%");
                        })
                            ->orWhere('id', $search)
                            ->limit(10)
                            ->get()
                            ->map(function ($profile) {
                                return [
                                    'id' => $profile->id,
                                    'name' => $profile->full_name . ' - ' . $profile->user->email,
                                ];
                            });
                        return $results->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->required(),

                TextInput::make('custom_amount')->numeric(),
                Select::make('custom_currency')
                    ->options(config('currencies.currencies')),

                Select::make('status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'ready_for_collection' => 'Ready for collection',
                        'paid' => 'Paid',
                    ])

            ])->columns(1);
    }
}
