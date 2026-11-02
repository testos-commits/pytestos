<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class News extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $guarded = ['id'];
    protected $with = ['countries'];
    public $translatable = ['title', 'description', 'content'];

    public function likes()
    {
        return $this->hasMany(NewsLike::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'news_countries');
    }

    public function scopeLikesInfo($query)
    {
        return $query->withCount('likes')->with(['likes' => function ($query) {
            $query->where('user_profile_id', auth()->user()->profile->id); //checks if the current user has liked the news
        }]);
    }

    public function getImageAttribute($value)
    {
        if (request()->is('api/*')) {
            //write your logic for api call
            return  config('aws.aws-urls.admin_base_url') . $value;
        }
        return $value;
    }
}
