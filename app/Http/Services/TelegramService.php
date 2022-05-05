<?php

namespace App\Http\Services;

use App\Models\City;
use App\Models\Profile;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;


class TelegramService
{

    private function generateCityButtons()
    {
        $cities = City::get();
        $cities_btn = array();
        foreach ($cities as $city) {
            array_push($cities_btn, [['text' => $city->title, 'callback_data' => $city->id]]);
        }
        return $cities_btn;
    }

    private function sendBotMessage($chat_id, $text, $reply_markup = [])
    {
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => $reply_markup
        ]);
    }

    private function handleContactPhoneMessage($cities_btn_btn, $chat_id, $phone_number)
    {
        Profile::where('chat_id', '=', $chat_id)
            ->update([
                'phone_number' => $phone_number
            ]);
        $keyboard = Keyboard::make([
            'inline_keyboard' => $cities_btn_btn,
            'one_time_keyboard' => true
        ]);
        $this->sendBotMessage($chat_id, 'Выберите город', $keyboard);
    }

    private function handleCityEditBtn($cities_btn_btn, $call_back_message, $cityId_selected)
    {
        $keyboard = Keyboard::make([
            'inline_keyboard' => $cities_btn_btn,
            'one_time_keyboard' => true
        ]);
        Telegram::editMessageText([
            'chat_id' => $call_back_message['chat']['id'],
            'message_id' => $call_back_message['message_id'],
            'reply_markup' => $keyboard,
            'text' => $call_back_message['text'],
        ]);

        Profile::where('chat_id', '=', $call_back_message['chat']['id'])
            ->update(['city_id' => $cityId_selected]);

        // ask full name

        $forceReply = Keyboard::forceReply(['force_reply' => true]);
        $this->sendBotMessage(
            $call_back_message['chat']['id'],
            Profile::REPLY_ACTION_ENTER_FULL_NAME,
            $forceReply
        );
    }

    // update db full_name and ask dob
    private function handleFullNameMessage($chat_id, $text)
    {
        Profile::where('chat_id', '=', $chat_id)
            ->update(['full_name' => $text]);

        $forceReply = Keyboard::forceReply(['force_reply' => true, 'input_field_placeholder' => 'dd/mm/YYYY']);

        $this->sendBotMessage(
            $chat_id,
            Profile::REPLY_ACTION_ENTER_DOB,
            $forceReply
        );
    }

    // update db dob and ask nickname
    private function handleDobMessage($chat_id, $text)
    {
        try {
            $dob = Carbon::createFromFormat('d/m/Y', $text);
            Profile::where('chat_id', '=', $chat_id)
                ->update(['dob' => $dob]);

            $forceReply = Keyboard::forceReply(['force_reply' => true]);
            $this->sendBotMessage(
                $chat_id,
                Profile::REPLY_ACTION_ENTER_NICKNAME,
                $forceReply
            );
        }catch (InvalidFormatException $invalidFormatException){
            Telegram::sendMessage([
                'chat_id' => $chat_id,
                'parse_mode' => 'html',
                'text' => '<b>Неверная дата рождения, введите ее в формате «дд/мм/гггг»!!!</b>',
            ]);

            $forceReply = Keyboard::forceReply(['force_reply' => true, 'input_field_placeholder' => 'dd/mm/YYYY']);

            $this->sendBotMessage(
                $chat_id,
                Profile::REPLY_ACTION_ENTER_DOB,
                $forceReply
            );

            return;
        }


    }

    // update db dob and finished
    private function handleNicknameMessage($chat_id, $text)
    {
        $profile_update = Profile::where('chat_id', '=', $chat_id)->first();
        $profile_update->nickname = $text;
        $profile_update->save();
        $keyboard = Keyboard::make()
           ->row(
                Keyboard::button(['text' => '/read']),
            )->row(
                Keyboard::button(['text' => '/edit']),
            );
        if(isset($profile_update->city) and $profile_update->phone_number and $profile_update->nickname and $profile_update->dob){
            Telegram::sendMessage([
                'chat_id' => $chat_id,
                'parse_mode' => 'html',
                'text' => '<b>Операция прошла успешно!!</b>',
                'reply_markup'=>$keyboard
            ]);
            return;
        }

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'parse_mode' => 'html',
            'text' => '<b>Ошибка!!</b>',
        ]);
    }

    public function webHookMessage($data)
    {
        try {
            if (!isset($data['message']['entities']) && isset($data['message']['entities']['type']) != "bot_command") {

                if (isset($data['message']['contact'])) {
                    // create city button
                    $cities_btn = $this->generateCityButtons();

                    // update db phone_number and ask city
                    $chat_id = $data['message']['chat']['id'];
                    $phone_number = $data['message']['contact']['phone_number'];
                    $this->handleContactPhoneMessage($cities_btn, $chat_id, $phone_number);

                    return;
                }
                if (isset($data['callback_query'])) {
                    // edit btn after choosing city, and update db
                    $cityId_selected = $data['callback_query']['data'];
                    $city_selected = City::where('id', '=', $cityId_selected)->first();
                    $cities_btn = array([['text' => '✅' . $city_selected->title, 'callback_data' => $city_selected->id]]);
                    $this->handleCityEditBtn($cities_btn, $data['callback_query']['message'], $cityId_selected);

                    return;
                }
                if (isset($data['message']['reply_to_message'])) {
                    switch ($data['message']['reply_to_message']['text']) {
                        case Profile::REPLY_ACTION_ENTER_FULL_NAME:
                            $this->handleFullNameMessage($data['message']['chat']['id'], $data['message']['text']);
                            break;
                        case Profile::REPLY_ACTION_ENTER_DOB:
                            $this->handleDobMessage($data['message']['chat']['id'], $data['message']['text']);
                            break;
                        case Profile::REPLY_ACTION_ENTER_NICKNAME:
                            $this->handleNicknameMessage($data['message']['chat']['id'], $data['message']['text']);
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::debug($e);
            report($e);
        }
    }
}
