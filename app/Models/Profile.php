<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use Carbon\Carbon;
use App\Traits\RequiresSubscription;

class Profile extends Model
{
    use RequiresSubscription;
    use HasFactory;
    use Filterable;
    protected $guarded = ['id'];

    protected $with = ['countryCode'];
    protected $table = 'user_profiles';

    protected $casts = [
        'meta' => 'array',
        'previous_commercials' => 'array',
        'social_media_accounts' => 'array',
    ];

    protected $appends = ['age', 'is_verified'];

    /** RELATIONSHIPS */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_profile_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function nationalities()
    {
        return $this->belongsToMany(Country::class, 'profile_nationalities', 'user_profile_id', 'country_id');
    }

    public function passports()
    {
        return $this->belongsToMany(Country::class, 'profile_passports', 'user_profile_id', 'country_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditionApplications()
    {
        return $this->hasMany(AuditionApplication::class, 'user_profile_id');
    }

    public function castingImages()
    {
        return $this->hasMany(CastingImage::class, 'user_profile_id');
    }


    public function profileImages()
    {
        return $this->hasMany(ProfileImage::class, 'user_profile_id');
    }

    public function hasVideo()
    {
        return  $this->video ? true : false;
    }

    public function hasCastingImages()
    {
        return $this->castingImages->count() ? true : false;
    }

    public function getAvatar()
    {
        if (count($this->castingImages)) {
            return $this->castingImages[0]->image;
        }

        return null;
    }

    public function applied()
    {
        return $this->belongsToMany(Audition::class, 'auditions_applications', 'user_profile_id', 'audition_id')
            ->wherePivot('is_shortlisted', false)
            ->wherePivot('is_accepted', false);
    }

    public function shortlisted()
    {
        return $this->belongsToMany(Audition::class, 'auditions_applications', 'user_profile_id', 'audition_id')
            ->wherePivot('is_shortlisted', true)
            ->wherePivot('is_accepted', false);
    }

    public function isVerified()
    {
        return $this->video && $this->castingImages->count() ? true : false;
    }

    public function getIsVerifiedAttribute()
    {
        return $this->isVerified();
    }

    public function getVideoThumbnailAttribute($value)
    {
        if (!$value) return null;

        return $value;
    }

    public function getCleanMobileAttribute()
    {
        if ($this->attributes['phone'] == null || $this->attributes['phone'] == '') {
            return null;
        }

        $cleanMobile = $this->attributes['phone'];

        if (substr($cleanMobile, 0, 2) === '00') {
            $cleanMobile = substr($cleanMobile, 2);
        }

        if (substr($cleanMobile, 0, 1) === '+') {
            $cleanMobile = substr($cleanMobile, 1);
        }

        if (substr($cleanMobile, 0, 1) === '±') {
            $cleanMobile = substr($cleanMobile, 1);
        }

        if (substr($cleanMobile, 0, 3) !== $this->country->country_code) {
            $cleanMobile = $this->countryCode ? $this->countryCode->country_code . $cleanMobile : $cleanMobile;
        }
        return str_replace(' ', '', $cleanMobile);
    }

    public function adminDisplayNationalities()
    {
        $value = implode(', ', $this->nationalities->map(fn($n) => $n->name)->toArray());

        return $value !== '' ? $value : 'Not Specified';
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function adminDisplayPassports()
    {
        $value = implode(', ', $this->passports->map(fn($n) => $n->name)->toArray());

        return $value !== '' ? $value : 'Not Specified';
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->attributes['date_of_birth'])->age;
    }
    public function accepted()
    {
        return $this->belongsToMany(Audition::class, 'auditions_applications', 'user_profile_id', 'audition_id')
            ->wherePivot('is_shortlisted', false)
            ->wherePivot('is_accepted', true);
    }

    public function messages()
    {
        return $this->belongsToMany(Message::class, 'audition_messages_profiles', 'user_profile_id', 'audition_message_id')
            ->withPivot('audition_messages_option_id', 'read', 'created_at', 'updated_at', 'read_by_admin')
            ->orderBy('audition_messages_profiles.created_at', 'desc');
    }

    public function profileMessages()
    {
        return $this->hasMany(ProfileMessage::class, 'user_profile_id');
    }

    public function unreadProfileMessages()
    {
        return $this->hasMany(ProfileMessage::class, 'user_profile_id')->where('read', false);
    }

    public function countryCode()
    {
        return $this->belongsTo(Country::class, 'country_code_country_id', 'id');
    }

    /** METHODS */
    public function canApply()
    {
        $status = true;
        $message = '';

        if (!$this->hasVideo()) {
            $status = false;
            $message = 'VIDEO_MISSING';
        }

        if (!$this->hasCastingImages()) {
            $status = false;
            $message = 'CASTING_IMAGES_MISSING';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    public function canUploadPhotos()
    {
        return $this->hasAccessToPremiumFeatures(auth()->user()->id)
            && $this->profileImages()
            ->count() < config('premium-feature-limits.photos_per_profile');
    }

    public function scopeHasExpoToken($query)
    {
        return $query->with(['user' => function ($query) {
            $query->token();
        }])->whereHas('user', function ($query) {
            // , function ($subQuery) {
            //     // Explicitly define the morph relationship parameters
            //     $subQuery->where('owner_type', '=', \App\Models\User::class);
            // }
            $query->whereHas('expoToken')
            ->whereNotNull('email_verified_at');
        });
    }
}
