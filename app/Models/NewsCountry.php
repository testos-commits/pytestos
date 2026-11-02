<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCountry extends Model
{
    use HasFactory;
    protected $table = 'news_countries';
    protected $guarded = ['id'];

    public function news() {
        return $this->belongsTo(News::class);
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }
}
