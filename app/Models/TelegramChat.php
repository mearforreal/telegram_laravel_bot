<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramChat extends Model
{
    use HasFactory;

    protected $table = 'telegram_chat';

    protected $fillable = [
        'update_id','message_id',
        'chat_id','date','message_text','replied'
    ];
}
