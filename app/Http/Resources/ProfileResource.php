<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AuditionResource;
use App\Http\Resources\SocialMediaResource;

class ProfileResource extends JsonResource
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
            "id" => $this->id,
            "old_user_id" => $this->old_user_id ? (string) $this->old_user_id . '_legacy' : (string) $this->id . '_new_profile',
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "description" => $this->description,
            "country" => new CountryResource($this->country),
            "country_code" => new CountryResource($this->countryCode),
            'nationalities' => CountryResource::collection($this->nationalities),
            'passports' => CountryResource::collection($this->passports),
            "date_of_birth" => $this->date_of_birth,
            "city" => $this->city,
            "street" => $this->street,
            "building" => $this->building,
            "phone" => $this->phone,
            "gender" => $this->gender,
            "major" => $this->major,
            "university" => $this->university,
            "occupation" => $this->occupation,
            "weight" => $this->weight,
            "height" => $this->height,
            "shoes" => $this->shoes,
            "pants" => $this->pants,
            "shirt" => $this->shirt,
            "video" => $this->video,
            "video_thumbnail" => $this->video_thumbnail ? $this->video_thumbnail . '?t=' . strtotime($this->updated_at) : null,
            "is_default" => $this->is_default,
            "meta" => $this->meta ?? [],
            "filters" => $this->filters,
            "casting_images" => CastingImageResource::collection($this->typedCastingImages ?? []),
            "profile_images" => ProfileImageResource::collection($this->profileImages ?? []),
            "accepted_auditions" => AuditionResource::collection($this->accepted) ?? [],
            "social_media" => SocialMediaResource::collection($this->social_media ?? []),
            'previous_commercials' => $this->previous_commercials,
            'is_first_login' => $this->is_first_login ?? false,
            'payments' => $this->payments ?? null,
            'is_verified' => $this->is_verified,
            'wallet_last_updated' => $this->wallet_updated_on,
            'aws_config' => $this->country->aws_bucket ? config("aws-buckets.{$this->country->aws_bucket}") : config('aws-buckets.default'),
            'has_unread_notifications' => $this->hasUnreadNotifications,
            'can_add_photos' => $this->canUploadPhotos(),
            'is_main' => $this->is_main,
        ];
    }
}
