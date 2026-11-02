<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CastingImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // return parent::toArray($request);/

        return [
            'id' => $this->id,
            'image' => $this->image,
            'reference_image' => $this->reference_image,
            'casting_image_type_id' => $this->casting_image_type_id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
