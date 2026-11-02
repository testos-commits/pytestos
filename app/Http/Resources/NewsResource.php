<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cleanText = strip_tags($this->content);
        $cleanText = html_entity_decode($cleanText);
        $cleanText = trim($cleanText);

        $summary = strlen($cleanText) > 100 ? substr($cleanText, 0, 100) . '...' : $cleanText;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'summary' => $summary,
            'likes' => $this->likes_count,
            'liked' => $this->likes->count() > 0, // is it liked by requester?,
            'image' => $this->image,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
