<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/understandme" command
 *
 * Returns the IBM Bluemix processing data
 */
class UnderstandMeCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'understandme';

    /**
     * @var string
     */
    protected $description = 'Returns the IBM Watson understanding of your sentence';

    /**
     * @var string
     */
    protected $usage = '/understandme <text>';

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
            $text = 'Command usage: ' . $this->getUsage();
        }

        $client = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);
        $res = $client->get(
            'understandme',
            ['query' => ['text' => $text]]
        );

        $data = [
            'chat_id' => $chat_id,
            'text'    => json_decode($res->getBody()),
        ];

        return Request::sendMessage($data);
    }
}