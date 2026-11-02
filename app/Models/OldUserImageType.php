<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldUserImageType extends Model
{
    use HasFactory;

    protected $connection = 'old';

    protected $table = 'image_types';

    public function images()
    {
        return $this->hasMany(OldUserImage::class, 'type_id', 'id');
    }
}
