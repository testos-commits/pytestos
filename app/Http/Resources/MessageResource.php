<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $profileMessage = $this->profileMessage->first();

        $data = [
            'id' => $this->id,
            'profile_message_id' => $profileMessage ? $profileMessage->id : null,
            'audition_id' => $this->audition_id,
            'audition' => $this->when($this->audition, new AuditionResource($this->audition)),
            'message' => $this->message,
            'for_applicants' => $this->for_applicants,
            'for_shortlisted' => $this->for_shortlisted,
            'for_accepted' => $this->for_accepted,
            'answer' =>  $profileMessage ? $profileMessage->answer : null,
            'options' => MessageOptionResource::collection($this->options),
            'created_at' => $this->created_at->diffForHumans(),
        ];


        return $data;
    }
}
