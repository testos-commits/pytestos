<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldUserImage extends Model
{
    use HasFactory;

    protected $connection = 'old';
    protected $table = 'user_images';

    public function type()
    {
        return $this->belongsTo(OldUserImageType::class, 'image_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(OldUser::class, 'user_id', 'id');
    }
}
