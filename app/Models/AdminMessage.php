<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AdminMessage extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $guarded = ['id'];
    public $translatable = ['message'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
