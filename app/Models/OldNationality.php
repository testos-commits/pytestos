<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldNationality extends Model
{
    use HasFactory;

    protected $connection = 'old';
    protected $table = 'nationalities';


}
