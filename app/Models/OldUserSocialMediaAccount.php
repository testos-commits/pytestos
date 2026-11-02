<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldUserSocialMediaAccount extends Model
{
    use HasFactory;
    protected $table = 'user_social_media_accounts';
    protected $connection = 'old';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function socialMediaPlatform()
    {
        return $this->belongsTo(OldSocialMediaPlatform::class, 'platform_id', 'id');
    }
}
