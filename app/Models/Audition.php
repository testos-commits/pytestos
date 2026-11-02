<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use Spatie\Translatable\HasTranslations;

class Audition extends Model
{
    use HasFactory;
    use Filterable;
    use HasTranslations;

    protected $guarded = ['id'];
    protected $casts = [
        'meta' => 'array',
    ];

    public $translatable = ['title', 'description', 'image', 'location', 'duration'];

    protected $appends = ['total_budget'];

    /** RELATIONSHIPS */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @deprecated
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'audition_country', 'audition_id', 'country_id');
    }

    public function applications()
    {
        return $this->hasMany(AuditionApplication::class, 'audition_id');
    }

    public function scopeWithUserApplications($query, $profileId)
    {
        $query->with([
            'applications' => function ($query) use ($profileId) {
                $query->where('user_profile_id', $profileId);
            }
        ]);
    }
    // This scope makes sure the audition does not belong to a project that was soft deleted    
    public function scopeProjectActive($query)
    {
        $query->whereHas('project', function ($query) {
            $query->where('deleted_at', null);
        });
    }

    public function applicationStatusesUpdatable()
    {
        return $this->project->deleted_at === null;
    }
    public function applicants()
    {
        return $this->belongsToMany(Profile::class, 'auditions_applications', 'audition_id', 'user_profile_id')
            ->wherePivot('is_shortlisted', false)
            ->wherePivot('is_accepted', false)
            ->withPivot(['id', 'in_selection', 'updated_at'])
            ->with('castingImages')
            ->withTimestamps()
            ->orderByPivot('updated_at', 'desc');
    }

    public function shortlisted()
    {
        return $this->belongsToMany(Profile::class, 'auditions_applications', 'audition_id', 'user_profile_id')
            ->wherePivot('is_shortlisted', true)
            ->wherePivot('is_accepted', false)
            ->withPivot(['id', 'in_selection', 'updated_at'])
            ->with('castingImages')
            ->withTimestamps()
            ->orderByPivot('updated_at', 'desc');
    }

    public function accepted()
    {
        return $this->belongsToMany(Profile::class, 'auditions_applications', 'audition_id', 'user_profile_id')
            ->wherePivot('is_shortlisted', false)
            ->wherePivot('is_accepted', true)
            ->withPivot(['id', 'in_selection', 'updated_at'])
            ->with('castingImages')
            ->withTimestamps()
            ->orderByPivot('updated_at', 'desc');
    }

    public function selected()
    {
        return $this->belongsToMany(Profile::class, 'auditions_applications', 'audition_id', 'user_profile_id')
            ->wherePivot('in_selection', true)
            ->withPivot(['id', 'in_selection', 'updated_at'])
            ->with('castingImages')
            ->withTimestamps()
            ->orderByPivot('updated_at', 'desc');
    }

    private function searchApplicants($query, $searchQuery, $filters = [])
    {
        return function ($query) use ($searchQuery, $filters) {
            if (!empty($searchQuery)) {
                $query->where('first_name', 'LIKE', "%$searchQuery%")
                    ->orWhere('last_name', 'LIKE', "%$searchQuery%")
                    ->orWhere('user_profiles.id', $searchQuery);
            }

            foreach ($filters as $key => $value) {
                if (is_array($value) && count($value) > 0) {
                    if (strpos($key, 'meta_') !== false) {
                        $key = str_replace('meta_', '', $key);
                        $query->whereJsonContains("meta->{$key}", $value);
                    }
                } else {
                    if ($key == 'gender' && $value !== null && $value !== '') {
                        $query->where('gender', $value);
                    }

                    if ($key === 'min_age' && $value !== null && $value !== '') {
                        $query->where('date_of_birth', '<=', now()->subYears($value));
                    }

                    if ($key === 'max_age' && $value !== null && $value !== '') {
                        $query->where('date_of_birth', '>=', now()->subYears($value));
                    }
                }
            }
        };
    }

    public function scopeWithAllApplications($query, $searchQuery = '', $filters = [])
    {
        $query->with([
            'applicants' => $this->searchApplicants($query, $searchQuery, $filters),
            'shortlisted' => $this->searchApplicants($query, $searchQuery, $filters),
            'accepted' => $this->searchApplicants($query, $searchQuery, $filters),
            'selected' => $this->searchApplicants($query, $searchQuery, $filters)
        ])
            ->withCount([
                'applicants',
                'shortlisted',
                'accepted',
                'selected',
            ]);

        return $query;
    }

    public function scopeWithAllApplicationsExtended($query, $closure)
    {
        return $query->with([
            'applicants' => $closure,
            'shortlisted' => $closure,
            'accepted' => $closure,
            'selected' => $closure,
        ])
            ->withCount([
                'applicants' => $closure,
                'shortlisted' => $closure,
                'accepted' => $closure,
                'selected' => $closure,
            ]);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    /** SCOPES */
    public function scopeWithProfileMessages($query, $profileId)
    {
        $query->with([
            'profileMessages' => function ($query) use ($profileId) {
                $query->where('user_profile_id', $profileId)->orderBy('created_at');
            },
            'messages.options',
            'messages' => function ($query) use ($profileId) {
                $query->whereHas('profileMessage', function ($subquery) use ($profileId) {
                    $subquery->where('user_profile_id', $profileId);
                });
            },
            'messages.profileMessage' => function ($query) use ($profileId) {
                $query->where('user_profile_id', $profileId);
            }
        ])
            ->whereHas('messages', function ($query) use ($profileId) {
                $query->whereHas('profileMessage', function ($subquery) use ($profileId) {
                    $subquery->where('user_profile_id', $profileId);
                });
            });
    }

    public function scopeFilterProject($query, $projectId)
    {
        $query->where('project_id', $projectId);
    }
    public function scopeFilterCountry($query, $countryId)
    {
        $query->whereHas('countries', function ($query) use ($countryId) {
            $query->where('country_id', $countryId);
        });
    }

    /** ACCESSORS */

    public function getTotalBudgetAttribute()
    {
        $filming_budget = 0;
        $usage_budget = 0;

        if (isset($this->filming_budget)) {
            $filming_budget = $this->filming_budget;
        }

        if (isset($this->usage_budget)) {
            $usage_budget = $this->usage_budget;
        }

        $total = $filming_budget + $usage_budget;

        return $total > 0 ? $total : 'N/A';
    }

    public function getImageAttribute($value)
    {
        if (request()->is('api/*')) {
            //write your logic for api call
            return config('aws.aws-urls.admin_base_url') . $value;
        }
        return $value;
    }

    public function isExpired()
    {
        return new DateTime($this->applications_deadline) < new DateTime() && $this->applications_deadline !== null;
    }

    public function isTerminated()
    {
        return $this->is_terminated;
    }
}
