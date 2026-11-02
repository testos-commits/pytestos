<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\Attribute;
use App\Models\CastingImageType;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CreateProfile extends CreateRecord
{
    protected static string $resource = ProfileResource::class;
    protected $castingImagesTemp = [];
    protected $nationalityIds = [];
    protected $passportIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $commercials = [];
        if (isset($data['previous_commercials'])) {
            foreach ($data['previous_commercials'] as $commercial) {
                $commercials[] = $commercial['name'];
            }
        }

        $data['previous_commercials'] = $commercials;

        // Store casting images temporarily
        $castingImages = [];
        $castingImageTypes = CastingImageType::all();
        foreach ($castingImageTypes as $type) {
            $fieldName = 'casting_image_' . $type->id;
            if (isset($data[$fieldName]) && $data[$fieldName]) {
                $castingImages[$type->id] = $data[$fieldName];
            }
            unset($data[$fieldName]);
        }

        if (isset($data['video']) && !empty($data['video'])) {
            $s3BaseUrl = config('filesystems.disks.s3_admin.url');
            // add the bucket's region to the base URL if it's not already included before "amazonaws.com"
            if (!str_contains($s3BaseUrl, config('filesystems.disks.s3_admin.region'))) {
                $s3BaseUrl = str_replace('amazonaws.com', config('filesystems.disks.s3_admin.region') . '.amazonaws.com', $s3BaseUrl);
            }

            $data['video'] = $s3BaseUrl . $data['video'];
        }

        // Store nationalities and passports temporarily
        $this->castingImagesTemp = $castingImages;
        $this->nationalityIds = $data['nationality_ids'] ?? [];
        $this->passportIds = $data['passport_ids'] ?? [];

        $data['meta'] = $this->extractAttributeMeta($data);

        unset($data['nationality_ids']);
        unset($data['passport_ids']);

        return $data;
    }

    protected function getAttributeFields(): array
    {
        $fields = [];

        foreach (Attribute::query()->get() as $attribute) {
            $options = [];

            foreach (($attribute->options ?? []) as $key => $option) {
                $options[$key] = $option['en'] ?? $key;
            }

            $fields[] = Select::make('meta_' . $attribute->slug)
                ->label($attribute->name)
                ->multiple()
                ->options($options)
                ->searchable()
                ->preload();
        }

        return $fields;
    }

    protected function extractAttributeMeta(array &$data): array
    {
        $meta = [];
        $attributeSlugs = Attribute::query()->pluck('slug')->all();

        foreach ($attributeSlugs as $slug) {
            $fieldName = 'meta_' . $slug;

            if (array_key_exists($fieldName, $data)) {
                $selectedValues = array_values(array_filter(
                    $data[$fieldName] ?? [],
                    static fn($value) => $value !== null && $value !== ''
                ));

                if ($selectedValues !== []) {
                    $meta[$slug] = $selectedValues;
                }

                unset($data[$fieldName]);
            }
        }

        return $meta;
    }

    protected function afterCreate(): void
    {
        // Save casting images
        if (isset($this->castingImagesTemp) && !empty($this->castingImagesTemp)) {
            $s3BaseUrl = config('filesystems.disks.s3_admin.url');
            // add the bucket's region to the base URL if it's not already included before "amazonaws.com"
            if (!str_contains($s3BaseUrl, config('filesystems.disks.s3_admin.region'))) {
                $s3BaseUrl = str_replace('amazonaws.com', config('filesystems.disks.s3_admin.region') . '.amazonaws.com', $s3BaseUrl);
            }

            foreach ($this->castingImagesTemp as $typeId => $image) {
                $this->record->castingImages()->create([
                    'casting_image_type_id' => $typeId,
                    'image' => $s3BaseUrl . $image,
                ]);
            }
        }



        // Sync nationalities
        if (isset($this->nationalityIds) && !empty($this->nationalityIds)) {
            $this->record->nationalities()->sync($this->nationalityIds);
        }

        // Sync passports
        if (isset($this->passportIds) && !empty($this->passportIds)) {
            $this->record->passports()->sync($this->passportIds);
        }
    }

    public function form(Form $form): Form
    {
        $castingImageTypes = CastingImageType::all();
        $castingImageFields = [];

        foreach ($castingImageTypes as $type) {
            $castingImageFields[] = FileUpload::make('casting_image_' . $type->id)
                ->label($type->name)
                ->image()
                ->disk('s3_admin')
                ->directory(config('aws-bucket-directories.profile_casting_images'))
                ->visibility('public')
                ->maxSize(5120);
        }

        return $form
            ->schema([
                Tabs::make('Profile Information')->tabs([
                    Tabs\Tab::make('Basic Information')
                        ->schema([
                            Select::make('user_id')
                                ->label('User Account')
                                ->relationship('user', 'email')
                                ->searchable()
                                ->required()
                                ->helperText('Select the parent user account for this profile'),

                            TextInput::make('first_name')->required(),
                            TextInput::make('last_name')->required(),
                            Textarea::make('description'),
                            Select::make('country_id')
                                ->label('Country')
                                ->relationship(
                                    name: 'country',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true),
                                )
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                                ->required(),

                            DatePicker::make('date_of_birth')->required(),
                            TextInput::make('city'),
                            TextInput::make('street'),
                            TextInput::make('building'),
                            Grid::make(2)
                                ->schema([
                                    Select::make('country_code_country_id')
                                        ->label('Country Code')
                                        ->required()
                                        ->relationship(
                                            name: 'countryCode',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true),
                                        )
                                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->country_code} ({$record->name})")
                                        ->searchable()
                                        ->preload(),
                                    TextInput::make('phone'),
                                ]),
                            TextInput::make('alternative_phone_number'),
                            Select::make('gender')->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])->required(),                            
                            TextInput::make('university'),
                            TextInput::make('major'),
                            TextInput::make('occupation'),
                            TextInput::make('weight'),
                            TextInput::make('height'),
                            TextInput::make('shoes'),
                            TextInput::make('pants'),
                            TextInput::make('shirt'),
                            FileUpload::make('video')
                                ->acceptedFileTypes(['video/*'])
                                ->disk('s3_admin')
                                ->directory(config('aws-bucket-directories.profile_casting_videos'))
                                ->visibility('public')
                                ->maxSize(20480),
                            Repeater::make('previous_commercials')
                                ->schema([
                                    TextInput::make('name'),
                                ]),
                            Checkbox::make('is_first_login')->default(true),
                            Checkbox::make('is_blacklisted')->default(false),
                        ]),
                    Tabs\Tab::make('Casting Images')
                        ->schema($castingImageFields)
                        ->columns(2),
                    Tabs\Tab::make('Nationalities & Passports')
                        ->schema([
                            Select::make('nationality_ids')
                                ->label('Nationalities')
                                ->multiple()
                                ->options(\App\Models\Country::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->helperText('Select one or more nationalities'),
                            Select::make('passport_ids')
                                ->label('Passports')
                                ->multiple()
                                ->options(\App\Models\Country::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->helperText('Select one or more passports'),
                        ]),
                    Tabs\Tab::make('Attributes')
                        ->schema($this->getAttributeFields())
                        ->columns(2),
                ]),
            ])->columns(1);
    }
}
