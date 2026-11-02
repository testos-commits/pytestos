<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use App\Forms\Components\UserSelector;
use Illuminate\Contracts\View\View;
use App\Models\AuditionApplication;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Closure;
use Illuminate\Database\Eloquent\Model;
use App\Services\MessageService;
use App\Traits\HasFilamentTranslatableFields;
use App\Jobs\NotifyAppUsers;

class CreateMessage extends CreateRecord
{
    use HasFilamentTranslatableFields;
    /** @todo fix cancel button url
     *  @todo fix redirect after create
     */
    protected static string $resource = MessageResource::class;

    public $auditionId;

    public $applications;
    public $selectedRecipients = [];
    public  $selectedIds = []; // Keeps track of the selected ids so when search happens and the query loads it excludes the selected ids
    protected $translatableFields = ['message', 'answer_options' => ['option']];
    public function mount(): void
    {

        static::authorizeResourceAccess();

        $this->auditionId = request()->auditionId;


        $this->applications = AuditionApplication::where('audition_id', request()->auditionId)->with('profile.castingImages')->get();

        if (request()->recipientId) {
            $this->selectedIds[] = request()->recipientId;


            $this->selectedRecipients[] = $this->applications->filter(function (AuditionApplication $application) {
                return $application->user_profile_id == request()->recipientId;
            })->first();


            $this->applications = $this->applications->filter(function (AuditionApplication $application) {
                return $application->user_profile_id != request()->recipientId;
            })->values();

            $this->data['manually_select'] = true;
            $this->data['recipients'] = $this->selectedIds;
        }
    }


    public function getHeader(): ?View
    {
        return view('components.layouts.header', [
            'backLink' => $this->getResource()::getUrl('audition-message-chats', ['record' => $this->auditionId]),
            'backText' => 'Back to messages',
            'title' => null,
            'buttonText' => null,
            'buttonLink' => null,
            'placeholder' => null,
            'subtitle' => null
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return MessageService::create($data);
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('audition-message-chats', ['record' => $this->auditionId]);
    }

    protected function recipientsConfigured()
    {
        return (isset($this->data['for_applicants']) && $this->data['for_applicants'])
            || (isset($this->data['for_shortlisted']) && $this->data['for_shortlisted'])
            || (isset($this->data['for_accepted']) && $this->data['for_accepted'])
            || (isset($this->data['manually_select']) && $this->data['manually_select']);
    }

    protected function beforeValidate(): void
    {

        if (!isset($this->data['manually_select'])) {
            $this->data['manually_select'] = false;
        }

        if (!isset($this->data['for_applicants'])) {
            $this->data['for_applicants'] = false;
        }

        if (!isset($this->data['for_shortlisted'])) {
            $this->data['for_shortlisted'] = false;
        }

        if (!isset($this->data['for_accepted'])) {
            $this->data['for_accepted'] = false;
        }
    }

    protected function checkboxValidation()
    {
        return function () {
            return function (string $attribute, $value, Closure $fail) {
                if (!$this->recipientsConfigured()) {
                    $fail('Please configure recipients');
                }
            };
        };
    }

    protected function afterCreate(): void
    {
        $applicants = [];

        if (!$this->data['manually_select']) {
            $allApplicants = AuditionApplication::where([
                'audition_id' => $this->auditionId,
            ])->get();

            if ($this->data['for_applicants']) {
                $applicants[] = $allApplicants
                    ->where('is_shortlisted', false)
                    ->where('is_accepted', false)
                    ->pluck('user_profile_id')
                    ->all();
            }


            if ($this->data['for_accepted']) {
                $applicants[] = $allApplicants
                    ->where('is_accepted', true)
                    ->pluck('user_profile_id')
                    ->all();
            }

            if ($this->data['for_shortlisted']) {
                $applicants[] = $allApplicants
                    ->where('is_shortlisted', true)
                    ->pluck('user_profile_id')
                    ->all();;
            }

            $applicants = collect($applicants)
                ->flatten()
                ->unique()
                ->toArray();
        } else {
            $applicants = $this->data['recipients'];
        }
        NotifyAppUsers::dispatch(
            'NEW_MESSAGE',
            $this->getRecord()->audition,
            $applicants,
            'some'
        )->onQueue('notification-chunks');

     
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->setMutatedData($data);

        $data['audition_id'] = $this->auditionId;

        if (isset($data['manually_select'])) {
            unset($data['manually_select']);
        }

        return $data;
    }

    /**
     * Moves the recipient from applicants to selected recipients
     */
    public function addToSelectedRecipients($index)
    {
        $recipientId =  $this->applications[$index]->user_profile_id;
        $this->selectedRecipients[] = $this->applications[$index];
        $this->selectedIds[] = $recipientId;
        $this->data['recipients'] = $this->selectedIds;

        $this->applications = $this->applications->filter(function (AuditionApplication $application) use ($recipientId) {
            return $application->user_profile_id != $recipientId;
        });

        $this->applications = $this->applications->values();
    }

    /**
     * Moves the recipient from applicants to selected recipients
     */
    public function removeFromSelectedRecipients($index)
    {

        $this->applications[] = $this->selectedRecipients[$index];

        if (($key = array_search($this->selectedRecipients[$index]->user_profile_id, $this->selectedIds)) !== false) {
            unset($this->selectedIds[$key]);
        }

        unset($this->selectedRecipients[$index]);

        $this->selectedRecipients = array_values($this->selectedRecipients);
    }

    public function form(Form $form): Form
    {

        return   $form->schema([
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Create Message')
                        ->schema([
                            ...$this->makeTranslatableField('message', Textarea::class),
                            Repeater::make('answer_options')
                                ->schema([
                                    ...$this->makeTranslatableField('option', TextInput::class),
                                ])
                                ->columns(2)
                        ]),
                    Tabs\Tab::make('Configure Recipients')
                        ->schema([
                            Checkbox::make('for_applicants')
                                ->label('For Applicants')
                                ->live()
                                ->afterStateUpdated(function (set $set, ?string $state) {
                                    if ($state) {
                                        $set('manually_select', false);
                                    }
                                })->rules([$this->checkboxValidation()]),
                            Checkbox::make('for_shortlisted')->label('For Shortlisted')
                                ->live()
                                ->afterStateUpdated(function (set $set, ?string $state) {
                                    if ($state) {
                                        $set('manually_select', false);
                                    }
                                })->rules([$this->checkboxValidation()]),
                            Checkbox::make('for_accepted')->label('For Accepted')->live()->afterStateUpdated(function (set $set, ?string $state) {
                                if ($state) {
                                    $set('manually_select', false);
                                }
                            })->rules([$this->checkboxValidation()]),
                            Checkbox::make('manually_select')->label('Manually Select Recipients')->live()->afterStateUpdated(function (set $set, ?string $state) {
                                if ($state) {
                                    $set('for_applicants', false);
                                    $set('for_shortlisted', false);
                                    $set('for_accepted', false);
                                }
                            }),
                            TextInput::make('search_applicants')->label('Search for applicants')
                                ->live(debounce: 500)
                                ->hidden(fn(Get $get): bool => !$get('manually_select'))
                                ->afterStateUpdated(function (?string $state) {
                                    $this->applications = AuditionApplication::where('audition_id', $this->auditionId)
                                        ->whereNotIn('id', $this->selectedIds)
                                        ->whereHas('profile', function ($query) use ($state) {
                                            $query->where('first_name', 'like', '%' . trim($state) . '%')
                                                ->orWhere('id', (int) $state);
                                        })
                                        ->with('profile.castingImages')
                                        ->get();
                                }),
                            UserSelector::make('recipients')
                                ->live(debounce: 500)
                                ->hidden(fn(Get $get): bool => !$get('manually_select'))
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            if ($this->data['manually_select'] && count($this->selectedIds) == 0) {
                                                $fail('Please select at least one recipient');
                                            }
                                        };
                                    },
                                ]),
                        ])
                ])
        ]);
    }
}
