<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'user_profile_id')->with('user');
    }

    public function audition()
    {
        return $this->belongsTo(Audition::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeProjectAccounting($query, $projectId)
    {
        return $query->with(['project' => function ($query) use ($projectId) {
            $query->where('id', $projectId);
        }])->where('project_id', $projectId);
    }

    public function scopeAuditionAccounting($query, $auditionId)
    {
        return $query->with(['audition' => function ($query) use ($auditionId) {
            $query->where('id', $auditionId);
        }])->where('audition_id', $auditionId);
    }

    public function getIconAttribute()
    {
        return $this->status;
    }
}
