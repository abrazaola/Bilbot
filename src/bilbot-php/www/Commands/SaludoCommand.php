<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Bilbot\Constants;
use Bilbot\PhraseRandomizer;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/saludo" command
 *
 * Greetings from Bilbot!
 */
class SaludoCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'saludo';

    /**
     * @var string
     */
    protected $description = 'Saludos de Bilbot!';

    /**
     * @var string
     */
    protected $usage = '/saludo';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $data = [
            'chat_id' => $chat_id,
            'text'    => PhraseRandomizer::getRandomPhrase(Constants::PHRASE_GREETINGS_COMMAND),
        ];

        return Request::sendMessage($data);
    }
}