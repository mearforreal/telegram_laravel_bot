<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number','full_name',
        'nickname','dob','city_id','chat_id'
    ];

    const REPLY_ACTION_ENTER_FULL_NAME = "Введите имя и фамилию";
    const REPLY_ACTION_ENTER_DOB = "Введите день рождения";
    const REPLY_ACTION_ENTER_NICKNAME = "Введите никнейм";

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
