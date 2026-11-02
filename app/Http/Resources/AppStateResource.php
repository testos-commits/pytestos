<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'is_maintenance' => $this->is_maintenance,
            'current_version' => $this->current_version,
            'popup_title' => $this->popup_title,
            'popup_message' => $this->popup_message,
            'show_button' => $this->show_button,
        ];
    }
}
