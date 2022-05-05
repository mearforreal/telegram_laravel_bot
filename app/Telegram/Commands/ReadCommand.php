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

        $profile = Profile::where('chat_id','=',$chat_id)->with('city')->first();

        $format = "<b>Информация профиля</b>"."\n\n".
            "<b>Номер телефона:</b> <i>%s</i>"."\n".
            "<b>Город:</b> <i>%s</i>"."\n".
            "<b>Никнейм:</b> <i>%s</i>"."\n".
            "<b>Дата рождения:</b> <i>%s</i>";

        $text = '<b>Регистрация не завершена</b>, используйте /edit для обновления профиля';
        if(isset($profile->city) and $profile->phone_number and $profile->nickname and $profile->dob){
            $text = sprintf($format, $profile->phone_number, $profile->city->title,$profile->nickname,$profile->dob);
        }
        $this->replyWithMessage([
            'parse_mode'=>'html',
            'text' => $text,
        ]);

    }
}
