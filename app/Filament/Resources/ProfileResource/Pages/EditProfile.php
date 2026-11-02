<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\Attribute;
use App\Models\CastingImageType;
use App\Services\FileDownloadService;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;
    protected ?string $originalVideo = null;
    protected ?string $videoToDelete = null;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }



    protected function mutateFormDataBeforeFill(array $data): array
    {
        $commercials = [];

        if (isset($data['previous_commercials'])) {
            foreach ($data['previous_commercials'] as $commercial) {
                $commercials[] = ['name' => $commercial];
            }
        }
        $data['user_email'] = $this->record->user->email;
        $data['previous_commercials'] = $commercials;
        $this->originalVideo = $data['video'] ?? null;

        if (isset($data['video']) && !empty($data['video'])) {
            $s3BaseUrl = $this->getS3AdminBaseUrl();

            if (str_starts_with($data['video'], $s3BaseUrl)) {
                $data['video'] = ltrim(substr($data['video'], strlen($s3BaseUrl)), '/');
            } else {
                $data['video'] = null;
            }
        }

        // Load casting images by type
        $castingImageTypes = CastingImageType::all();
        foreach ($castingImageTypes as $type) {
            $image = $this->record->castingImages()->where('casting_image_type_id', $type->id)->first();
            $data['casting_image_' . $type->id] = $image ? $image->image : null;
        }

        // Load nationalities and passports
        $data['nationality_ids'] = $this->record->nationalities->pluck('id')->toArray();
        $data['passport_ids'] = $this->record->passports->pluck('id')->toArray();
        $data = $this->fillAttributeMeta($data);

        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $commercials = [];
        if (isset($data['previous_commercials'])) {
            foreach ($data['previous_commercials'] as $commercial) {
                $commercials[] = $commercial['name'];
            }
        }

        $data['previous_commercials'] = $commercials;

        $this->videoToDelete = null;

        if (array_key_exists('video', $data)) {
            if (!empty($data['video'])) {
                $data['video'] = $this->buildS3AdminMediaUrl($data['video']);

                if ($this->originalVideo && $data['video'] !== $this->originalVideo) {
                    $this->videoToDelete = $this->originalVideo;
                }
            } else {
                unset($data['video']);
            }
        }

        // Handle casting images
        $castingImageTypes = CastingImageType::all();
        foreach ($castingImageTypes as $type) {
            $fieldName = 'casting_image_' . $type->id;
            if (isset($data[$fieldName]) && $data[$fieldName]) {
                // Update or create the casting image
                $this->record->castingImages()->updateOrCreate(
                    ['casting_image_type_id' => $type->id],
                    ['image' => $this->buildS3AdminMediaUrl($data[$fieldName])]
                );
            }
            unset($data[$fieldName]);
        }

        // Handle nationalities
        if (isset($data['nationality_ids'])) {
            $this->record->nationalities()->sync($data['nationality_ids']);
            unset($data['nationality_ids']);
        }

        // Handle passports
        if (isset($data['passport_ids'])) {
            $this->record->passports()->sync($data['passport_ids']);
            unset($data['passport_ids']);
        }

        $data['meta'] = $this->extractAttributeMeta($data);

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

    protected function fillAttributeMeta(array $data): array
    {
        foreach ((is_array($data['meta'] ?? null) ? $data['meta'] : []) as $slug => $selectedValues) {
            $data['meta_' . $slug] = $selectedValues;
        }

        return $data;
    }

    protected function extractAttributeMeta(array &$data): array
    {
        $attributeSlugs = Attribute::query()->pluck('slug')->all();
        $meta = array_diff_key(
            is_array($this->record->meta) ? $this->record->meta : [],
            array_flip($attributeSlugs)
        );

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

    protected function afterSave(): void
    {
        if (!$this->videoToDelete) {
            return;
        }

        $disk = FileDownloadService::getVideoBucketConfig($this->videoToDelete);

        if (!$disk) {
            return;
        }

        $fileName = ltrim(parse_url($this->videoToDelete, PHP_URL_PATH), '/');

        if (Storage::disk($disk)->exists($fileName)) {
            Storage::disk($disk)->delete($fileName);
        }

        $this->videoToDelete = null;
        $this->originalVideo = $this->record->video;
    }

    protected function getS3AdminBaseUrl(): string
    {
        $s3BaseUrl = config('filesystems.disks.s3_admin.url');

        // add the bucket's region to the base URL if it's not already included before "amazonaws.com"
        if (!str_contains($s3BaseUrl, config('filesystems.disks.s3_admin.region'))) {
            $s3BaseUrl = str_replace('amazonaws.com', config('filesystems.disks.s3_admin.region') . '.amazonaws.com', $s3BaseUrl);
        }

        return $s3BaseUrl;
    }

    protected function buildS3AdminMediaUrl(string $path): string
    {
        return $this->getS3AdminBaseUrl() . ltrim($path, '/');
    }

    public  function form(Form $form): Form
    {
        $castingImageTypes = CastingImageType::all();
        $castingImageFields = [];

        foreach ($castingImageTypes as $type) {
            $castingImageFields[] = FileUpload::make('casting_image_' . $type->id)
                ->label($type->name)
                ->image()
                ->disk('s3_admin')
                ->directory('profile-casting-images')
                ->visibility('public')
                ->maxSize(5120)
                ->helperText('Upload one image for ' . $type->name);
        }

        return $form
            ->schema([
                Tabs::make('Profile Information')->tabs([
                    Tabs\Tab::make('Basic Information')
                        ->schema([
                            TextInput::make('id')->label('Identifier')->disabled(),

                            TextInput::make('user_email')
                                ->label('Parent account email')
                                ->disabled(),

                            TextInput::make('user_id')
                                ->label('Parent account ID')
                                ->disabled(),

                            TextInput::make('first_name')->required(),
                            TextInput::make('last_name')->required(),
                            Textarea::make('description'),
                            Select::make('country')
                                ->options(\App\Models\Country::all()->pluck('name', 'id'))
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
                            ]),
                            TextInput::make('major'),
                            TextInput::make('university'),
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
                            Checkbox::make('is_first_login'),
                            Checkbox::make('is_blacklisted'),
                        ]),
                    Tabs\Tab::make('Casting Images')
                        ->schema($castingImageFields)
                        ->columns(2),
                    Tabs\Tab::make('Nationalities & Passports')
                        ->schema([
                            Select::make('nationality_ids')
                                ->label('Nationalities')
                                ->multiple()
                                ->relationship(
                                    name: 'nationalities',
                                    titleAttribute: 'name',
                                    // modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true),
                                )
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                                ->preload()
                                ->searchable()
                                ->helperText('Select one or more nationalities'),
                            Select::make('passport_ids')
                                ->label('Passports')
                                ->multiple()
                                ->relationship(
                                    name: 'passports',
                                    titleAttribute: 'name',
                                    // modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true),
                                )
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name}")
                                ->preload()
                                ->searchable()
                                ->helperText('Select one or more passports'),
                        ]),
                    Tabs\Tab::make('Attributes')
                        ->schema($this->getAttributeFields())
                        ->columns(2),
                ]),
            ])->columns(1);
    }
}
