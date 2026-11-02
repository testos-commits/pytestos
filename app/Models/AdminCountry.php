<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminCountry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function admin() {
        return $this->belongsTo(User::class, 'user_id')->whereHas('roles', function ($query) {
            $query->where('name', 'country admin');
        });
    }

    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
