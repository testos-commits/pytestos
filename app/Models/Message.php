<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Message extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $table = "audition_messages";
    protected $guarded = ['id'];
    protected $append = ['targeted'];
    public $translatable = ['message'];


    public function audition()
    {
        return $this->belongsTo(Audition::class);
    }

    public function options()
    {
        return $this->hasMany(MessageOption::class, 'audition_message_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(Profile::class, 'audition_messages_profiles', 'audition_message_id', 'user_profile_id')
            ->withPivot('audition_messages_option_id', 'read', 'created_at');
    }

    public function profileMessage()
    {
        return $this->hasMany(ProfileMessage::class, 'audition_message_id');
    }

    public function getTargetedAttribute($value)
    {
        $available = ['for_applicants', 'for_shortlisted', 'for_accepted'];
        $texts = [
            'for_applicants' => 'applicants',
            'for_shortlisted' => 'shortlisted',
            'for_accepted' => 'accepted'
        ];

        $targeted = [];

        foreach ($available as $target) {
            if ($this->{$target}) {
                $targeted[] = $texts[$target];
            }
        }

        if (sizeof($targeted) == 0) {
            return 'Direct Message';
        }

        return 'For ' . implode(', ', $targeted);
    }

    public function scopeWithChat($query, $auditionId)
    {
        $profileId = auth()->user()->profile->id;

        return $query->with([
            'options',
            'audition',
            'profileMessage' => function ($query) use ($profileId) {
                $query->where('user_profile_id', $profileId);
            },
        ])->whereHas('profileMessage', function ($subquery) use ($profileId) {
            $subquery->where('user_profile_id', $profileId);
        })
            ->where('audition_id', $auditionId);
    }
}
