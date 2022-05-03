<?php

namespace App\Http\Services;

use App\Models\Country;
use App\Models\Profile;
use App\Models\TelegramChat;
use App\Models\TelegramContact;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;


class TelegramService{

    public function webHookMessage($data){
        if (!isset($data['message']['entities']) && isset($data['message']['entities']['type']) != "bot_command") {
            $countries = Country::get();
            $countries_btn = array();
            foreach ($countries as $country){
                array_push($countries_btn,[['text' => $country->title,'callback_data' => $country->id]]);
            }

            if (isset($data['message']['contact'])) {
//                $profile = new Profile();
//                $profile->phone_number = $data['message']['contact']['phone_number'];
//

                $keyboard = Keyboard::make([
                    'inline_keyboard' => $countries_btn,
                    'one_time_keyboard' => true
                ]);

                //✅

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
//                TelegramChat::create([
//                    'update_id' => $data['update_id'],
//                    'message_id' => $data['message']['message_id'],
//                    'chat_id' => $data['message']['chat']['id'],
//                    'message_text' => $data['message']['text'],
//                    'replied' => false,
//                    'date' => Carbon::parse($data['message']['date']),
//                ]);
                if(isset($data['callback_query'])){
                    try {

                        $countryId_selected = $data['callback_query']['data'];
                        $country_selected = Country::where('id','=',$countryId_selected)->first();
                        $search_index = 0;
                        for($i = 0; $i < count($countries_btn); $i++){
                            if($country[$i][0]['callback_data'] == $countryId_selected){
                                $search_index = $i;
                            }
                        }
                        $countries_btn[$search_index] = [['text' => $country_selected->title,'' => $country_selected->id]];

                        Telegram::sendMessage([
                            'chat_id' => $data['callback_query']['message']['chat']['id'],
                            'text' => '✅'.json_encode($countries_btn[$search_index]['text']). ' id:'.$countries_btn['callback_data'],
                        ]);


                        $btn_index = array_search($countries_btn,[['text' => $country_selected->title,'callback_data' => $country_selected->id]]);
//                        $country_selected[$btn_index] = ['text' => '✅'.$country_selected->title,'callback_data' => $country_selected->id];
//                        $button = Keyboard::make([
//                            'inline_keyboard' => $countries_btn,
//                        ]);
//                        Log::debug([
//                            'chat_id' => $data['callback_query']['message']['chat']['id'],
//                            'text' =>  $data['callback_query']['message']['text'],
//                            'reply_markup' => $button,
//                            'message_id' => $data['callback_query']['message']['message_id'],
//                        ]);

                    }catch (\Exception $e){
                        Log::debug($e);
                    }

                }
            }
        }
    }
}
