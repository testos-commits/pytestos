<?php

namespace App\Filament\Resources\AuditionResource\Pages;

use App\Filament\Resources\AuditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use App\Services\GeneralService;
use App\Traits\HasFilamentTranslatableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Services\MessageService;
use App\Models\AuditionApplication;
use App\Jobs\NotifyAppUsers;

class EditAudition extends EditRecord
{
    protected static string $resource = AuditionResource::class;
    // use EditRecord\Concerns\Translatable;
    use HasFilamentTranslatableFields;

    protected $translatableFields = ['title', 'description', 'location', 'duration', 'image'];

    public $projectId;
    public $metaFields = [];

    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->projectId = request()->projectId ? request()->projectId : $this->getRecord()->project_id;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index') . '?projectId=' .  $this->getRecord()->project_id;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = $this->getMutatedData($data);

        if (isset($data['meta'])) {
            foreach ($data['meta'] as $slug => $meta) {
                $data['meta_' . $slug] = $meta;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->setMutatedData($data);

        $data['show_budget'] = $data['show_budget'] ?? false;

        $keys = array_keys($data);

        $data['meta'] = [];

        foreach ($keys as $key) {
            if (strpos($key, 'meta_') !== false) {
                $slug = str_replace('meta_', '', $key);

                if (sizeof($data[$key]) > 0) {
                    $data['meta'][$slug] = $data[$key];
                }

                unset($data[$key]);
            }
        }


        return $data;
    }


    public function getMetaFields()
    {
        $generalService = new GeneralService();
        // $currentMeta = $this->record->meta;

        $fields = [];
        $attributes = $generalService->getAttributes()->all();

        foreach ($attributes as $slug => $attribute) {
            $options = [];

            foreach ($attribute->options as $key => $option) {
                $options[$key] = $option['en'];
            }

            $fields[] = Select::make('meta_' . $slug)
                ->multiple()
                ->label($attribute->name)
                ->options($options);
        }

        return $fields;
    }


    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.auditions.index', ['projectId' => $this->projectId]) => 'Auditions',
            '' => 'Edit'
        ];
    }

    public function TerminateAction()
    {
        return Actions\Action::make('Terminate')
            ->action(function (): void {
                $updateData = [
                    'is_terminated' => true
                ];

                // if ($this->record->applications_deadline === null) {
                // $updateData['applications_deadline'] = now()->subDay()->format('Y-m-d');
                // }

                $this->record->update($updateData);

                MessageService::create([
                    "for_applicants" => true,
                    "for_accepted" => false,
                    "recipients" => [],
                    "for_shortlisted" => true,
                    "message" => [
                        "en" => __('messages.AUDITION_TERMINATED', [], 'en'),
                        "ar" => __('messages.AUDITION_TERMINATED', [], 'ar'),
                        "el" => __('messages.AUDITION_TERMINATED', [], 'el'),
                    ],
                    "audition_id" => $this->record->id
                ]);

                $notificationRecipients = AuditionApplication::where([
                    'audition_id' => $this->record->id,
                    'is_accepted' => false,
                ])
                    ->get()
                    ->pluck('user_profile_id');

                NotifyAppUsers::dispatch(
                    'NEW_MESSAGE',
                    $this->record->id,
                    $notificationRecipients,
                    'some'
                )->onQueue('notification-chunks');
            })
            ->successNotificationTitle('This audition is now terminated, users cannot apply to it anymore')
            ->successRedirectUrl(null)
            ->requiresConfirmation();
    }
    public function publishAction()
    {
        return Actions\Action::make('Publish')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription('Talents in this audition\'s countries will be able to see it and will get a notification.')
            ->action(function (): void {
                \App\Services\AuditionService::publish($this->record);
                $this->record->refresh();
            })
            ->successNotificationTitle('Published. Talents can now see this audition.');
    }

    protected function getHeaderActions(): array
    {
        return [
            ...($this->record->isDraft() ? [$this->publishAction] : []),
            ...(!$this->record->isTerminated() ? [$this->terminateAction] : []),
            Actions\DeleteAction::make()
                ->successRedirectUrl(route('filament.admin.resources.auditions.index', [
                    'projectId' => $this->projectId
                ])),

        ];
    }

    public function form(Form $form): Form
    {
        $genders = [];

        foreach (config('constants.genders') as $item) {
            $genders[$item['slug']] = $item['en'];
        }

        return $form
            ->schema([
                Tabs::make('Tabs')->tabs([
                    Tabs\Tab::make('Main infomation')
                        ->schema([
                            ...$this->makeTranslatableField('title', TextInput::class),
                            ...$this->makeTranslatableField('description', Textarea::class),
                            FileUpload::make('image_en')->disk('s3_admin')
                                ->directory(config('aws-bucket-directories.auditions'))
                                ->visibility('public')
                                ->image()
                                ->required()
                                ->label('Image (English)'),
                            FileUpload::make('image_ar')->disk('s3_admin')
                                ->directory(config('aws-bucket-directories.auditions'))
                                ->image()
                                ->visibility('public')
                                ->label('Image (Arabic)'),
                            ...$this->makeTranslatableField('location', TextInput::class),
                            ...$this->makeTranslatableField('duration', Textarea::class),
                        ]),
                    Tabs\Tab::make('Audition Details')
                        ->schema([
                            Select::make('countries')
                                ->multiple()
                                ->relationship(
                                    name: 'countries',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn(Builder $query) => $query->active()->authorized(),
                                )
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => $record->name)
                                ->preload()
                                ->required()
                                ->label('Countries'),
                            DatePicker::make('filming_date')->format('Y-m-d')->displayFormat('Y-m-d')->label('Filming Date'),
                            DatePicker::make('applications_deadline')->format('Y-m-d')->displayFormat('Y-m-d')->label('Applications Deadline'),
                            TextInput::make('filming_budget')->label('Filming Budget')->type('number')->required(),
                            TextInput::make('usage_budget')->label('Usage Budget')->type('number')->default(0),
                            Select::make('currency')->label('Currency')->options(config('currencies.currencies'))->required(),
                            Checkbox::make('show_budget')->label('Show Budget'),
                        ]),
                    Tabs\Tab::make('User Filters')
                        ->schema(
                            [
                                Grid::make([
                                    'default' => 2
                                ])
                                    ->schema([
                                        TextInput::make('min_age')->label('Minimum age')->type('number')->default(0)->required(),
                                        TextInput::make('max_age')->label('Maximum age')->type('number')->default(100)->required()
                                    ]),
                                Select::make('gender')->label('Gender')->options($genders),
                                ...$this->getMetaFields(),
                            ]
                        )
                ])
            ]);
    }
}
