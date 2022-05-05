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
    protected $description = 'Начать';

    /**
     * {@inheritdoc}
     */
    public function handle()

    {


//        $keyboard = Keyboard::make()
//            ->row(
//                Keyboard::button(['text' => '/register']),
//            )->row(
//                Keyboard::button(['text' => '/read']),
//            )->row(
//                Keyboard::button(['text' => '/edit']),
//            );

        $commands = $this->getTelegram()->getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        $text = "<b>Команды:</b>"."\n".$response;


        $this->replyWithMessage([
            'text' => $text,
            'parse_mode'=>'html'
        ]);

    }
}
