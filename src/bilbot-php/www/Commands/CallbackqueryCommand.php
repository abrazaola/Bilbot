<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;


class CallbackqueryCommand extends SystemCommand
{
    protected $name = 'callback';
    protected $description = 'Reply to callback query';
    protected $version = '1.0.0';

    public function execute()
    {
        $update = $this->getUpdate();
        $callback_query = $update->getCallbackQuery();
        $callback_data = $callback_query->getData();

        $entities = [
            'agenda' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'bikes' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'bpr' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'bs' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'clubs' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'dbr' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'hotels' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'nbr' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'restaurants' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'ta' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ],
            'to' => [
                'action' => 'agenda_event',
                'column' => 'title'
            ]
        ];

        $entityKey = explode('_', $callback_data, 2);

        if (
            !array_key_exists($entityKey[0], $entities)
        ) {
            return Request::emptyResponse();
        }

        $title = substr($callback_data, strlen($entityKey[0].'_'));

        $clientWelive = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WELIVE_API_ENDPOINT]);
        $resWelive = $clientWelive->get(
            $entities[$entityKey[0]]['action'],
            [
                'query' => [
                    $entities[$entityKey[0]]['column'] => $title,
                ]
            ]
        )->getBody()->getContents();
        $resWelive = json_decode($resWelive, true);

        if ($resWelive['count'] != 1) {
            return Request::sendMessage([
                'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                'text' =>
                    '¡Ups! Ha habido un problema y no puedo mostrarte esta información, ¿puedes probar en otro momento?',
            ]);
        }

        $eventInfoAnswer = $this->buildAnswer($entityKey[0], $resWelive);

        return Request::sendMessage([
            'chat_id' => $callback_query->getMessage()->getChat()->getId(),
            'text' => $eventInfoAnswer,
        ]);
    }

    private function buildAnswer($entityType, $data) {
        $answer = '';

        switch ($entityType) {
            case 'agenda':
                $answer =
                    'Sobre ' . $data['rows'][0]['titulo'] . PHP_EOL .
                    'tiene lugar en ' . $data['rows'][0]['lugar'] . PHP_EOL .
                    $data['rows'][0]['direccion'] . PHP_EOL .
                    'y está vigente hasta el ' . $data['rows'][0]['fecha_hasta'] . PHP_EOL;
                break;
        }

        return $answer;
    }
}