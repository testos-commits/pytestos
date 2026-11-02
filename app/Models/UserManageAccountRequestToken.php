<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserManageAccountRequestToken extends Model
{
    use HasFactory;
    protected $table = 'users_manage_account_request_tokens';
    protected $guarded = ['id'];
}
