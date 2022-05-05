<?php

namespace App\Telegram\Commands;

use App\Models\Profile;
use App\Models\TelegramChat;
use App\Models\TelegramContact;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ErrorHandler\Debug;
use Telegram\Bot\Commands\Command;
//use Telegram;
use Telegram\Bot\Keyboard\Keyboard;


/**
 * Class HelpCommand.
 */
class RegisterCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'register';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['registercommands'];

    /**
     * @var string Command Description
     */
    protected $description = 'Регистрация профиля';

    /**
     * {@inheritdoc}
     */
    public function handle()

    {
        $chat_id = $this->getUpdate()['message']['chat']['id'];


        $profile = Profile::firstOrNew(['chat_id' =>  $chat_id]);

        if (isset($profile->id) && isset($profile->phone_number) && isset($profile->full_name) && isset($profile->dob) && isset($profile->city_id)
        ){
            $this->replyWithMessage([
                'parse_mode'=>'html',
                'text' => '<b>Вы уже зарегистрированы</b>',
            ]);
            //$this->triggerCommand('read');
            return;
        }
            // request telephone
            $this->replyWithMessage([
                'parse_mode'=>'html',
                'text' => '<b>Регистрация профиля</b>',
            ]);
            $btn = Keyboard::button([
                'text' => 'Мой номер',
                'request_contact' => true
            ]);
            $keyboard = Keyboard::make([
                'keyboard' => [[$btn]],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);
            $this->replyWithMessage([
                'text' => 'Укажите номер телефона',
                'reply_markup' => $keyboard
            ]);


        $profile->save();

    }
}
