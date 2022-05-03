<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'update_id','message_id',
        'chat_id','date','phone_number','replied'
    ];
}
