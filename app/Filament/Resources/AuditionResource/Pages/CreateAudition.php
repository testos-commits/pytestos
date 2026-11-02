<?php

namespace App\Filament\Resources\AuditionResource\Pages;

use App\Filament\Resources\AuditionResource;
use Filament\Resources\Pages\CreateRecord;
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
use App\Services\NotificationsService;
use App\Traits\HasFilamentTranslatableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use App\Jobs\NotifyAppUsers;

class CreateAudition extends CreateRecord
{
    use HasFilamentTranslatableFields;

    protected static string $resource = AuditionResource::class;
    public $projectId;
    protected $translatableFields = ['title', 'description', 'location', 'duration', 'image'];


    public function mount(): void
    {
        parent::mount();
        $this->projectId = request()->projectId;
    }


    public function getHeader(): ?View
    {
        return view('filament.layout.back-button', [
            'backLink' => '/admin/auditions?projectId=' . $this->projectId,
            'backText' => 'Back to Project Auditions',
            'headerTitle' => 'Create Audition'
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->setMutatedData($data);

        $data['project_id'] = $this->projectId;

        $data['show_budget'] = $data['show_budget'] ?? false;
        
        if(auth()->user()->hasPermissionTo('set audition budget') === false) {
            $data['filming_budget'] = 0;
            $data['usage_budget'] = 0;
            $data['currency'] = null;
            $data['show_budget'] = false;
        }

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

    protected function afterCreate(): void
    {
        NotifyAppUsers::dispatch(
            'NEW_AUDITION',
            $this->getRecord(),
            $this->data['countries'],
            'audition_country'
        )->onQueue('notification-chunks');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index') . '?projectId=' .  $this->getRecord()->project_id;
    }

    public function getMetaFields()
    {
        $generalService = new GeneralService();

        $fields = [];
        $attributes = $generalService->getAttributes()->all();

        foreach ($attributes as $slug => $attribute) {
            $options = [];
            foreach ($attribute->options as $key => $option) {
                $options[$key] = $option['en'];
            }

            $fields[] = Select::make('meta_' . $slug)->multiple()->label($attribute->name)->options($options);
        }

        return $fields;
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
                            TextInput::make('filming_budget')->label('Filming Budget')->type('number')->required()->hidden(auth()->user()->hasPermissionTo('set audition budget') === false),
                            TextInput::make('usage_budget')->label('Usage Budget')->type('number')->default(0)->hidden(auth()->user()->hasPermissionTo('set audition budget') === false),
                            Select::make('currency')->label('Currency')->options(config('currencies.currencies'))->required()->hidden(auth()->user()->hasPermissionTo('set audition budget') === false),
                            Checkbox::make('show_budget')->label('Show Budget')->hidden(auth()->user()->hasPermissionTo('set audition budget') === false),
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
