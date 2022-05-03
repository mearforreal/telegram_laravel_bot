<?php

namespace App\Telegram\Commands;

use App\Models\TelegramChat;
use App\Models\TelegramContact;
use Illuminate\Support\Facades\Log;
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
    protected $description = 'Register command,register a profile';

    /**
     * {@inheritdoc}
     */
    public function handle()

    {

        $this->replyWithMessage([
            'text' => 'Регистрация профиля.',
        ]);
        // request telephone
        $btn = Keyboard::button([
            'text' => 'Мой номер',
            'request_contact' => true
        ]);
        $keyboard = Keyboard::make([
            'keyboard' => [[$btn]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        $response = $this->replyWithMessage([
            'text' => 'Укажите номер телефона',
            'reply_markup' => $keyboard
        ]);



//        $telegram_phone->replied = true;
//        $telegram_phone->save();
        $update = $response->getUpdate();


    }
}
