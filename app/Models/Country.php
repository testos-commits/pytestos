<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $guarded = ['id'];
    public $translatable = ['name'];

    public function admins()
    {
        return $this->belongsToMany(User::class, 'admin_countries', 'country_id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function auditions() {
        return $this->belongsToMany(Audition::class, 'audition_country', 'country_id', 'audition_id');
    }
    public function scopeAuthorized($query)
    {
        $user = User::find(auth()->user()->id);

        if ($user->hasRole('country admin')) {
            return $query->whereHas('admins', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
