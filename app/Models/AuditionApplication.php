<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditionApplication extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'auditions_applications';


    public function profile()
    {
        return $this->belongsTo(Profile::class, 'user_profile_id');
    }

    public function audition()
    {
        return $this->belongsTo(Audition::class, 'audition_id');
    }
}
