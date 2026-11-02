<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $guarded = ['id'];
    protected $casts = [
        'data' => 'array',
    ];

    public $translatable = ['title'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
