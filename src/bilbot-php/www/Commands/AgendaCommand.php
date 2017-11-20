<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/agenda" command
 *
 * Returns information about Bilbao's events agenda
 */
class AgendaCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'agenda';

    /**
     * @var string
     */
    protected $description = 'Información sobre la agenda de eventos de Bilbao';

    /**
     * @var string
     */
    protected $usage = '/agenda <texto>';

    /**
     * @var string
     */
    protected $version = '0.1.0';

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
        $text    = trim($message->getText(true));

        if ($text === '') {
            $res = 'Uso del comando: ' . $this->getUsage();
        } else {
            try {
                $client = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);
                $res = $client->get(
                    'understandme',
                    ['query' => ['text' => $text]]
                )->getBody()->getContents();
            } catch (Exception $e) {
                $res = json_encode(['error' => $e->getMessage()]);
            }
        }


        $data = [
            'chat_id' => $chat_id,
            'text'    => $res,
        ];

        return Request::sendMessage($data);
    }
}