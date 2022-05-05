<?php

namespace App\Telegram\Commands;

use App\Models\Profile;
use Telegram\Bot\Commands\Command;
use Telegram;

/**
 * Class HelpCommand.
 */
class ReadCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'read';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['readcommand'];

    /**
     * @var string Command Description
     */
    protected $description = 'Просмотр профиля';

    /**
     * {@inheritdoc}
     */
    public function handle()

    {
        $chat_id = $this->getUpdate()['message']['chat']['id'];

        $profile = Profile::where('chat_id','=',$chat_id)->with('country')->first();

        $format = "<b>Информация профиля</b>"."\n\n".
            "<b>Номер телефона:</b> <i>%s</i>"."\n".
            "<b>Город:</b> <i>%s</i>"."\n".
            "<b>Никнейм:</b> <i>%s</i>"."\n".
            "<b>Дата рождения:</b> <i>%s</i>";

        $text = sprintf($format, $profile->phone_number, $profile->country->title,$profile->nickname,$profile->dob);

        $this->replyWithMessage([
            'parse_mode'=>'html',
            'text' => $text,
        ]);

    }
}
