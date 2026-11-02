<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CastingImageType extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $guarded = ['id'];
    protected $table = 'casting_image_types';
    public $translatable = ['name', 'description'];
    public function castingImages()
    {
        return $this->hasMany(CastingImage::class, 'casting_image_type_id');
    }

    public function getReferenceImageAttribute($value)
    {
        if (request()->is('api/*')) {
            //write your logic for api call
            return config('aws.aws-urls.admin_base_url') . $value;
        }
        return $value;
    }

    public function scopeUserCastingImages($query, $userId)
    {
        return $query->with(['castingImages' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }]);
    }
}
