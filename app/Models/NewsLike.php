<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLike extends Model
{
    use HasFactory;
    protected $table = 'profile_news_likes';
    protected $guarded = ['id'];

    public function news() {
        return $this->belongsTo(News::class);
    }

    public function profile() {
        return $this->belongsTo(Profile::class, 'user_profile_id');
    }   
}
