<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProfilesExport implements WithMapping, WithHeadings, FromCollection
{

    public $profiles;
    public function __construct($profiles)
    {
        $this->profiles = $profiles;
    }

    public function collection()
    {
        return $this->profiles;
    }

    public function headings(): array
    {
        return [
            '#',
            'First name',
            'Country',
            'Nationalities',
            'Passports',
            'Age',
            'City',
            'Gender',
            'Major',
            'Weight',
            'Height',
            'Shirt size',
            'Pants size',
            'Shoe size',
        ];
    }
    public function map($profile): array
    {
        return [
            $profile->id,
            $profile->first_name,
            $profile->country->name,
            $profile->nationalities->map(function ($nationality) {
                return $nationality->name;
            })->implode(', '),
            $profile->passports->map(function ($passport) {
                return $passport->name;
            })->implode(', '),
            $profile->age,
            $profile->city,
            $profile->gender,
            $profile->major,
            $profile->weight,
            $profile->height,
            $profile->shirt,
            $profile->pants,
            $profile->shoes,
            // $this->generateFiltersText($profile),
        ];
    }


    public function generateFiltersText($profile)
    {
        $filtersTextArray = [];
        ;
        foreach ($profile->filters as $filter) {
            $optionsArray = [];

            foreach($filter['options'] as $option) {
                $optionsArray[] = $option;
            }
            $filtersTextArray[] = $filter['name'] . ': ' . implode(', ', $optionsArray);
        }
        return implode("\n", $filtersTextArray);
    }
}
