<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserSecurityAnswer extends Model
{
    protected $fillable = ['user_id', 'security_question_id', 'answer'];

    public function setAnswerAttribute($value)
    {
        $this->attributes['answer'] = Hash::make($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(SecurityQuestion::class, 'security_question_id');
    }
}