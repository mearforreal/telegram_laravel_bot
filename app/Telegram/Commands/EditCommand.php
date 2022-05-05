<?php

namespace App\Telegram\Commands;

use App\Models\Profile;
use Telegram\Bot\Commands\Command;
use Telegram;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class HelpCommand.
 */
class EditCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'edit';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['editcommands'];

    /**
     * @var string Command Description
     */
    protected $description = 'Редактирование профиля';

    /**
     * {@inheritdoc}
     */
    public function handle()

    {
        $chat_id = $this->getUpdate()['message']['chat']['id'];
        $profile = Profile::where('chat_id','=',$chat_id)->first();

        if ($profile === null){
            $this->replyWithMessage([
                'parse_mode'=>'html',
                'text' => '<b>Вы не зарегистрированы, пожалуйста, зарегистрируйтесь!!! </b>'."\n".'/register',
            ]);
            return;
//            $this->triggerCommand('read');
        }
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
            $this->replyWithMessage([
                'text' => 'Укажите номер телефона',
                'reply_markup' => $keyboard
            ]);
        }




}
