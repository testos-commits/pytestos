<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $lang = app()->getLocale();

        $options_localized = [];

        foreach ($this->options as $key => $option) {
            $options_localized[$key] = $option[$lang];
        }   

        $nameProperty = 'name';

        if($lang !== 'en') {
            $nameProperty = 'name_' . $lang;
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->{$nameProperty},
            'options' => $options_localized,
        ];
    }
}
