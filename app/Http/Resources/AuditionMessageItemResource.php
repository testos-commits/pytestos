<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditionMessageItemResource extends JsonResource
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
            'id' => $this->id,
            'profile_message_id' => sizeof($this->profileMessage) > 0 ? $this->profileMessage->first()->id: null,
            'message' => $this->message,
            'answer' =>sizeof($this->profileMessage) > 0 ? $this->profileMessage->first()->answer : null,
            'audition_id' => $this->audition_id,
            'for_applicants' => $this->for_applicants,
            'for_shortlisted' => $this->for_shortlisted,
            'for_accepted' => $this->for_accepted,
            'created_at' => $this->created_at->diffForHumans(),
            'options' => MessageOptionResource::collection($this->options),
        ];


    }
}
