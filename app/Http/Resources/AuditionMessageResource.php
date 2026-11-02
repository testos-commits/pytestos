<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class AuditionMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $lang = auth()->user()->app_language == 'en' ? '' : '_ar';

        return [
            'id' => $this['audition']['id'],
            'title' => $this['audition']['title'],
            'description' => $this['audition']['description'],
            'image' =>  $this['audition']['image'],
            'last_message_at' => isset($this['last_message_at']) ? $this['last_message_at']->diffForHumans() : null,
            'unread' => $this['unread'],
            'messages' =>  isset($this['messages']) ? AuditionMessageItemResource::collection($this['messages']) : [],
        ];
        // return [
        //     'id' => $this->audition->id,
        //     'title' => $this->audition->title,
        //     'description' => $this->audition->description,
        //     'image' =>  $this->audition->image,
        //     // 'last_message_at' => $this['last_message_at']->diffForHumans(),
        //     // 'unread' => $this['unread'],
        //     'messages' =>  AuditionMessageItemResource::collection($this->messages),
        // ];
    }
}
