<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Attribute;
use App\Http\Resources\CountryResource;
use App\Http\Resources\GenderResource;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\TextResource;
use App\Models\Faq;
use App\Http\Resources\FaqResource;
use App\Models\AppState;
use App\Http\Resources\AppStateResource;
use App\Models\Text;
use App\Traits\RequiresSubscription;

class GeneralService
{
    use RequiresSubscription;
    public function getGeneralData()
    {
        $countries = Country::all();
        $activeCountries = Country::where('is_active', 1)->get();
        $attributes = $this->getAttributes();
        
        $_genders = config('constants.genders');

        $lang = app()->getLocale();

        $free_period_message = config('free-subscription-period.message')[$lang];

        $genders = [];

        foreach ($_genders as $gender) {
            $genders[] = [
                'slug' => $gender['slug'],
                'name' => $gender[$lang]
            ];
        }

        $_subscription_texts = config('constants.subscription_texts');

        $subscription_texts = [];

        foreach ($_subscription_texts as $key => $texts) {
            $subscription_texts[$key] = $texts[$lang];
        }

        return [
            'countries' => CountryResource::collection($countries),
            'active_countries' => CountryResource::collection($activeCountries),
            'genders' => GenderResource::collection($genders),
            'attributes' => $attributes,
            'texts' => TextResource::collection(Text::all()),
            'faqs' => FaqResource::collection(Faq::all()),
            'subscription_texts' => $subscription_texts,
            'profile_limits' => config('premium-feature-limits.profiles_per_account'),
            'profile_photo_limits' => config('premium-feature-limits.photos_per_profile'),
            'free_period_message' => $free_period_message,
            'is_free_period_active' => $this->isFreePeriod()
        ];
    }

    public function getAttributes()
    {
        $attributes = Attribute::all()->keyBy('slug');
        return AttributeResource::collection($attributes);
    }

    public function getCurrentAppState()
    {
        return new AppStateResource(AppState::latest()->first());
    }
}
