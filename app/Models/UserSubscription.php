<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    
    public function scopeValid($query)
    {
        return $query->where('end_date', '>', now())->where('is_active', 1);
    }
}
