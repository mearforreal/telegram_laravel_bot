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


class TelegramService
{

    public function webHookMessage($data)
    {
        if (!isset($data['message']['entities']) && isset($data['message']['entities']['type']) != "bot_command") {

            $profile_data = [];
            $countries = Country::get();
            $countries_btn = array();
            foreach ($countries as $country) {
                array_push($countries_btn, [['text' => $country->title, 'callback_data' => $country->id]]);
            }

            if (isset($data['message']['contact'])) {

                $profile_data['phone_number'] = $data['message']['contact']['phone_number'];

                $keyboard = Keyboard::make([
                    'inline_keyboard' => $countries_btn,
                    'one_time_keyboard' => true
                ]);
                Telegram::sendMessage([
                    'chat_id' => $data['message']['chat']['id'],
                    'text' => 'Выбор города из списка',
                    'reply_markup' => $keyboard
                ]);
            } elseif (isset($data['callback_query'])) {
                try {
                    $countryId_selected = $data['callback_query']['data'];
                    $country_selected = Country::where('id', '=', $countryId_selected)->first();
                    $countries_btn = array([['text' => '✅' . $country_selected->title, 'callback_data' => $country_selected->id]]);
                    $keyboard = Keyboard::make([
                        'inline_keyboard' => $countries_btn,
                        'one_time_keyboard' => true
                    ]);
                    Telegram::editMessageText([
                        'chat_id' => $data['callback_query']['message']['chat']['id'],
                        'message_id' => $data['callback_query']['message']['message_id'],
                        'reply_markup' => $keyboard,
                        'text' => $data['callback_query']['message']['text'],
                    ]);

                    $profile_data['phone_number'] = $countryId_selected;

                    $forceReply = Keyboard::forceReply(['force_reply' => true]);

                    Telegram::sendMessage([
                        'chat_id' => $data['callback_query']['message']['chat']['id'],
                        'text' => 'Ввод имени и фамилии',
                        'force_reply' => true,
                        'reply_markup' => $forceReply
                    ]);


                } catch (\Exception $e) {
                    Log::debug($e);
                }

            }elseif (isset($data['message']['reply_to_message'])){
                if($data['message']['reply_to_message']['text'] == "Ввод имени и фамилии"){
                    $profile_data['full_name'] = $data['message']['text'];
                    $forceReply = Keyboard::forceReply(['force_reply' => true,'input_field_placeholder'=>'dd/mm/YYYY']);
                    Telegram::sendMessage([
                        'chat_id' => $data['message']['chat']['id'],
                        'text' => 'Ввод даты рождения',
                        'reply_markup' => $forceReply
                    ]);
                }
                if($data['message']['reply_to_message']['text'] == "Ввод даты рождения"){
                    $profile_data['dob'] = Carbon::createFromFormat('d/m/Y',$data['message']['text']);
                    $forceReply = Keyboard::forceReply(['force_reply' => true]);
                    Telegram::sendMessage([
                        'chat_id' => $data['message']['chat']['id'],
                        'text' => 'Ввод никнейма',
                        'reply_markup' => $forceReply
                    ]);
                }
                if($data['message']['reply_to_message']['text'] == "Ввод никнейма"){
                    $profile_data['nickname'] = $data['message']['text'];
                    Profile::create($profile_data);
                    Telegram::sendMessage([
                        'chat_id' => $data['message']['chat']['id'],
                        'text' => 'OKOK',
                    ]);
                }
            }
        }
    }
}
