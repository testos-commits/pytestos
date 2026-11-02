<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];

    public function auditions()
    {
        return $this->hasMany(Audition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'project_country', 'project_id', 'country_id');
    }

    public function scopeAuthorizedProjects($query)
    {
        $user = User::find(auth()->id());

        if ($user->hasRole('partner') || $user->hasRole('super director')) {
            return $query->where('user_id', $user->id);
        }

        if ($user->hasRole('country admin') ) {
            $countryIds = AdminCountry::where('user_id', $user->id)->pluck('country_id');

            if ($countryIds->isEmpty()) {
                return $query;
            }

            $query = $query->whereHas('countries', function ($query) use ($countryIds) {
                $query->whereIn('country_id', $countryIds);
            });
        }

        if ($user->hasRole('director')) {
            $projectIds =  DirectorProject::where('director_id', $user->id)->pluck('project_id');
            $query = $query->whereIn('id', $projectIds);
        }

        return $query;
    }

    public function authorize()
    {

        $user = User::find(auth('web')->id());

        if ($user->hasRole('partner') || $user->hasRole('super director')) {
            return $this->user_id = $user->id;
        }

        if ($user->hasRole('country admin')) {
            $countryIds = AdminCountry::where('user_id', $user->id)->pluck('country_id');

            $project = $this->withTrashed()
            ->where('id', $this->id)
            ->whereHas('countries', function ($query) use ($countryIds) {
                $query->whereIn('country_id', $countryIds);
            })
            ->first();
            
            return $project;
        }
        
        
        if ($user->hasRole('director') || $user->hasRole('super director')) {
            $projectIds =  DirectorProject::where('director_id', $user->id)->get()->pluck('project_id')->toArray();
            
            return in_array($this->id, $projectIds);
        }


        return $this->withTrashed()->find($this->id);
    }


    public function scopeWithProjectInfo($query)
    {
        return $query->withCount('auditions')->with(['auditions' => function ($query) {
            $query->withCount(['applications as accepted_count' => function ($query) {
                $query->where('auditions_applications.is_accepted', true);
            }]);

            $query->withCount(['applications as shortlisted_count' => function ($query) {
                $query->where('auditions_applications.is_shortlisted', true);
            }]);

            $query->withCount(['applications as applied_count' => function ($query) {
                $query->where('auditions_applications.is_shortlisted', false);
                $query->where('auditions_applications.is_accepted', false);
            }]);
        }])
            ->orderBy('created_at', 'desc');
    }
}
