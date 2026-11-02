<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastingImage extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'profile_casting_images';
    public function castingImageType()
    {
        return $this->belongsTo(CastingImageType::class, 'casting_image_type_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'user_profile_id');
    }

    public function getImageAttribute($value)
    {
        return $value;
    }
}
