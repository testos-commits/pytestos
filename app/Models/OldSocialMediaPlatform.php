<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldSocialMediaPlatform extends Model
{
    use HasFactory;
    protected $table = 'social_media_platforms';
    protected $connection = 'old';
}
