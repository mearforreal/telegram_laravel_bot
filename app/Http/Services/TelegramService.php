<?php

namespace App\Http\Services;

use App\Models\Country;
use App\Models\Profile;
use App\Models\TelegramChat;
use App\Models\TelegramContact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService{

    public function webHookMessage($data){
        if (!isset($data['message']['entities']) && isset($data['message']['entities']['type']) != "bot_command") {
            if (isset($data['message']['contact'])) {
//                $profile = new Profile();
//                $profile->phone_number = $data['message']['contact']['phone_number'];
//
                $countries = Country::get();

                $countries_btn = array();
                $keyboard = Keyboard::make()
                    ->inline();

                foreach ($countries as $country){
                    array_push($countries_btn,['text' => $country->title,'callback_data' => $country->id]);
                }

                $keyboard = Keyboard::make()
                    ->inline()->row($countries_btn);


//                $btn = Keyboard::button($countries_btn);
//                $keyboard = Keyboard::make([
//                    'inline_keyboard' => [$countries_btn],
//                ]);
//
                Telegram::sendMessage([
                    'chat_id' => $data['message']['chat']['id'],
                    'text' => 'Укажите номер qwe',
                    'reply_markup' => $keyboard
                ]);
//
//
//                $profile->phone_number = $data['message']['contact']['phone_number'];
//                TelegramContact::create([
//                    'update_id' => $data['update_id'],
//                    'message_id' => $data['message']['message_id'],
//                    'chat_id' => $data['message']['chat']['id'],
//                    'phone_number' => $data['message']['contact']['phone_number'],
//                    'replied' => false,
//                    'date' => Carbon::parse($data['message']['date']),
//                ]);
            } else {
                TelegramChat::create([
                    'update_id' => $data['update_id'],
                    'message_id' => $data['message']['message_id'],
                    'chat_id' => $data['message']['chat']['id'],
                    'message_text' => $data['message']['text'],
                    'replied' => false,
                    'date' => Carbon::parse($data['message']['date']),
                ]);
            }
        }
    }
}
