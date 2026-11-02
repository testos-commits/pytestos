<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditionCountry extends Model
{
    use HasFactory;
    protected $table = 'audition_country';
    protected $guarded = ['id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function audition()
    {
        return $this->belongsTo(Audition::class);
    }
}
