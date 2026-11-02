<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MessageOption extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'audition_messages_options';
    protected $guarded = ['id'];
    public $translatable = ['option'];
}
