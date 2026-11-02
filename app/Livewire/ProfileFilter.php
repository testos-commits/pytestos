<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use App\Services\GeneralService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProfileFilter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount($data): void
    {
        $this->data = $data;
        $this->form->fill();
    }


    public function getMetaFields()
    {
        $generalService = new GeneralService();

        $fields = [];
        $attributes = $generalService->getAttributes()->all();

        foreach ($attributes as $slug => $attribute) {
            $options = [];
            $selected = '';
            if (isset($this->data['data']['meta_' . $slug])) {
                $selected = $this->data['data']['meta_' . $slug];
            }
            foreach ($attribute->options as $key => $option) {
                $options[$key] = $option['en'];
            }

            $fields[] = Select::make('data.meta_' . $slug)->multiple()->label($attribute->name)->options($options)->default($selected);
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
                Select::make('data.country_id')
                    ->options(
                        function () {
                            return \App\Models\Country::query()
                                ->active()
                                ->authorized()
                                ->pluck('name', 'id');
                        }
                    )
                    ->default($this->data['data']['country_id'] ?? '')
                    ->label('Country'),
                Select::make('data.nationality_id')
                    ->searchable()
                    ->options(
                        function () {
                            return \App\Models\Country::query()
                                ->pluck('name', 'id');
                        }
                    )
                    ->default($this->data['data']['nationality_id'] ?? '')
                    ->label('Nationality'),
                Grid::make([
                    'default' => 2
                ])
                    ->schema([
                        TextInput::make('data.min_age')->label('Minimum age')->type('number')->default($this->data['data']['min_age'] ?? ''),
                        TextInput::make('data.max_age')->label('Maximum age')->type('number')->default($this->data['data']['max_age'] ?? ''),
                    ]),
                Select::make('data.gender')->label('Gender')->options($genders)->default($this->data['data']['gender'] ?? ''),
                ...$this->getMetaFields()
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->dispatch('filters-updated', $this->data['data']);
    }

    public function resetFilters()
    {
        $this->dispatch('reset-filters');
        $this->data['data'] = [];
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.profile-filter');
    }
}
