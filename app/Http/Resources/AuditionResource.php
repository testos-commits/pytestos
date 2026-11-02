<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $data = [
            'id' => $this->id,
            'project_ref' => '#' . $this->project_id,
            'countries' => CountryResource::collection($this->countries),
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'location' => $this->location,
            'filming_date' => $this->filming_date,
            'applications_deadline' =>  $this->applications_deadline,
            'duration' =>  $this->duration,
            'created_at' =>  $this->created_at->diffForHumans(),
            'filters' => $this->filters,
            'is_applied' => $this->applications->count() ? true : false,
            'project' => $this->project,
            'is_terminated' => $this->isTerminated(),
            'is_expired' => $this->isExpired(),
            // 'meta' => $this->meta,
        ];

        if ($this->show_budget) {
            $data['budget'] = (string)$this->total_budget . ' ' . $this->currency;
        }

        return $data;
    }
}
