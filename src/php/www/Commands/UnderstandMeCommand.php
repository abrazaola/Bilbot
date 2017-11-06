<?php

namespace Longman\TelegramBot\Commands\UserCommands;

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

        $config = [
            \Bilbot\Constants::WATSON_API_USERNAME,
            \Bilbot\Constants::WATSON_API_PASSWORD
        ];

        $client = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::WATSON_API_ENDPOINT]);
        $res = $client->get('analyze', ['auth' => $config, 'query' => [
            'version' => '2017-02-27',
            'text' => $text,
            'features' => 'sentiment,keywords,concepts,entities',
            'keywords.sentiment' => 'true'
        ]]);

        $data = [
            'chat_id' => $chat_id,
            'text'    => $res->getBody(),
        ];

        return Request::sendMessage($data);
    }
}