<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AppState extends Model
{
    use HasFactory;
    use HasTranslations;
    
    protected $guarded = ['id'];
    public $translatable = ['popup_message', 'popup_title'];


}
