<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardReferral extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function audition()
    {
        return $this->belongsTo(Audition::class, 'audition_id');
    }
}
