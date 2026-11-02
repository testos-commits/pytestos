<?php

namespace App\Traits;

use App\Models\Attribute;

trait Filterable
{
    protected $metaData = [''];

    public function initializeFilterable()
    {
        $this->append('filters');
    }

    /**
     * @param $entity - Audition or Project
     * @return $entity with localized filters
     */
    public function getFiltersFromMeta()
    {


        if (!$this->meta) return [];

        $attributes = Attribute::all();
        $attributesArray = [];

        $lang = app()->getLocale();

        $name = 'name';

        if ($lang == 'ar') {
            $name = 'name_ar';
        }

        foreach ($this->meta as $key => $values) {
            $attribute = $attributes->where('slug', $key)->first();

            if ($attribute) {
                $data = ['name' => $attribute->{$name}, 'options' => []];

                foreach ($values as $value) {
                    if (isset($attribute->options[$value][$lang])) {
                        $data['options'][] = $attribute->options[$value][$lang];
                    }
                }
                /** NOT SURE ABOUT THIS */
                // foreach ($values as $valueArray) {
                //     foreach ($valueArray as $value) {
                //         $data['options'][] = $attribute->options[$value][$lang];
                //     }
                // }
                $attributesArray[] = $data;
            }
        }

        return $attributesArray;
    }

    public function getFiltersAttribute()
    {
        return $this->getFiltersFromMeta();
    }
}
