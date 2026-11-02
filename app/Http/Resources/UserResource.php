<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\RequiresSubscription;

class UserResource extends JsonResource
{
    use RequiresSubscription;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       $isSubscribed = $this->validSubscription->first() && !$this->validSubscription->first()->is_free ? true : false;

       if($this->isFreePeriod()){
              $isSubscribed = true;
       }
        // return parent::toArray($request);
        return [
            'name' => $this->name,
            'email' => $this->email,
            'app_language' => $this->app_language,
            'profile' => new ProfileResource($this->profile),
            'profiles' => ProfileResource::collection($this->profiles),
            'referral_code' => $this->referral_code,
            'payment_details' => $this->paymentDetails,
            'subscribed' => $isSubscribed,
            'referrals' => $this->referrals ? ReferralRewardResource::collection($this->referrals) : null,
            'can_create_profile' => $this->canCreateProfile(),
            'can_switch_profiles' => $this->canSwitchProfiles(),
        ];
    }
}
