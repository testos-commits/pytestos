<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldUser extends Model
{
    use HasFactory;

    protected $connection = 'old';
    protected $table = 'users';

    public function images()
    {
        return $this->hasMany(OldUserImage::class, 'user_id', 'id');
    }

    public function nationality()
    {
        return $this->belongsTo(OldNationality::class);
    }

    public function socialMediaAccounts()
    {
        return $this->hasMany(OldUserSocialMediaAccount::class, 'user_id', 'id')->with('socialMediaPlatform');
    }
}
