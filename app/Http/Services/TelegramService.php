<?php

namespace App\Http\Services;

use App\Models\Country;
use App\Models\Profile;
use App\Models\TelegramChat;
use App\Models\TelegramContact;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Commands\CommandBus;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;


class TelegramService
{

    private function generateCountryButtons()
    {
        $countries = Country::get();
        $countries_btn = array();
        foreach ($countries as $country) {
            array_push($countries_btn, [['text' => $country->title, 'callback_data' => $country->id]]);
        }
        return $countries_btn;
    }

    private function sendBotMessage($chat_id, $text, $reply_markup = [])
    {
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => $reply_markup
        ]);
    }

    private function handleContactPhoneMessage($countries_btn, $chat_id, $phone_number)
    {
        Profile::where('chat_id', '=', $chat_id)
            ->update([
                'phone_number' => $phone_number
            ]);
        $keyboard = Keyboard::make([
            'inline_keyboard' => $countries_btn,
            'one_time_keyboard' => true
        ]);
        $this->sendBotMessage($chat_id, 'Выберите страну', $keyboard);
    }

    private function handleCountryEditBtn($countries_btn, $call_back_message, $countryId_selected)
    {
        $keyboard = Keyboard::make([
            'inline_keyboard' => $countries_btn,
            'one_time_keyboard' => true
        ]);
        Telegram::editMessageText([
            'chat_id' => $call_back_message['chat']['id'],
            'message_id' => $call_back_message['message_id'],
            'reply_markup' => $keyboard,
            'text' => $call_back_message['text'],
        ]);

        Profile::where('chat_id', '=', $call_back_message['chat']['id'])
            ->update(['country_id' => $countryId_selected]);

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
        Profile::where('chat_id', '=', $chat_id)
            ->update(['nickname' => $text]);

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'parse_mode' => 'html',
            'text' => '<b>Операция прошла успешно!!</b>',
        ]);
    }

    public function webHookMessage($data)
    {
        try {
            if (!isset($data['message']['entities']) && isset($data['message']['entities']['type']) != "bot_command") {

                if (isset($data['message']['contact'])) {
                    // create country button
                    $countries_btn = $this->generateCountryButtons();

                    // update db phone_number and ask country
                    $chat_id = $data['message']['chat']['id'];
                    $phone_number = $data['message']['contact']['phone_number'];
                    $this->handleContactPhoneMessage($countries_btn, $chat_id, $phone_number);

                    return;
                }
                if (isset($data['callback_query'])) {
                    // edit btn after choosing country, and update db
                    $countryId_selected = $data['callback_query']['data'];
                    $country_selected = Country::where('id', '=', $countryId_selected)->first();
                    $countries_btn = array([['text' => '✅' . $country_selected->title, 'callback_data' => $country_selected->id]]);
                    $this->handleCountryEditBtn($countries_btn, $data['callback_query']['message'], $countryId_selected);

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
        }
    }
}
