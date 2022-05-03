<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;


/**
 * Class StartCommand.
 */
class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['startcommand'];

    /**
     * @var string Command Description
     */
    protected $description = 'Start command, Get a list of all actions';

    /**
     * {@inheritdoc}
     */
    public function handle()

    {

//        $keyboard = Keyboard::make()
//            ->inline()
//            ->row(
//                Keyboard::inlineButton(['text' => 'Регистрация профиля', 'callback_data' => 'data1']),
//            )->row(
//                Keyboard::inlineButton(['text' => 'Просмотр профиля', 'callback_data' => 'data2']),
//            )->row(
//                Keyboard::inlineButton(['text' => 'Редактирование профиля', 'callback_data' => 'data3']),
//            );

        $keyboard = Keyboard::make()
            ->row(
                Keyboard::button(['text' => '/register', 'callback_data' => 'data1']),
            )->row(
                Keyboard::button(['text' => '/read', 'callback_data' => 'data2']),
            )->row(
                Keyboard::button(['text' => '/edit', 'callback_data' => 'data3']),
            );


        $response = $this->replyWithMessage([
            'text' => 'What do you want to do?',
            'reply_markup' => $keyboard
        ]);

        $messageId = $response->getMessageId();
        Log::debug($messageId);
        Log::debug($this->getUpdate());

        //$update = $this->getUpdate();


    }
}
