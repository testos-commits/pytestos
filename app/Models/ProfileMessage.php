<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileMessage extends Model
{
    use HasFactory;

    protected $table = "audition_messages_profiles";
    protected $guarded = ['id'];
    protected $with = ['answer'];

    public function message()
    {
        return $this->belongsTo(Message::class, 'audition_message_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'user_profile_id');
    }

    public function answer()
    {
        return $this->belongsTo(MessageOption::class, 'audition_messages_option_id');
    }
}
