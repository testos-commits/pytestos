<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectorProject extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'director_project';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }
}
