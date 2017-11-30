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
                'action' => 'agenda_detail',
                'column' => 'title'
            ],
            'bikes' => [
                'action' => 'bikes_detail',
                'column' => 'NOMBRE'
            ],
            'clubs' => [
                'action' => 'clubs_detail',
                'column' => '_id'
            ]
        ];

        $entityKey = explode('_', $callback_data, 2);

        if (
            !array_key_exists($entityKey[0], $entities)
        ) {
            return Request::emptyResponse();
        }

        $title = $this->decodeData($callback_data, $entityKey);

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

        if (
            (isset($resWelive['count']) && $resWelive['count'] != 1) ||
            (isset($resWelive['results']) && $resWelive['results']['count'] != 1)
        ) {
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
                    '📆 Sobre ' . $data['rows'][0]['titulo'] . PHP_EOL .
                    'tiene lugar en ' . $data['rows'][0]['lugar'] . PHP_EOL .
                    $data['rows'][0]['direccion'] . PHP_EOL .
                    'y está vigente hasta el ' . $data['rows'][0]['fecha_hasta'] . PHP_EOL;
                break;
            case 'bikes':
                $answer =
                    '🚲 Punto de recogida ' . $data['rows'][0]['NOMBRE'] . PHP_EOL .
                    'Libres de tipo A: ' . $data['rows'][0]['ALIBRES'] . PHP_EOL .
                    'Libres de tipo B: ' . $data['rows'][0]['BLIBRES'] . PHP_EOL .
                    '📍 Mapa => https://www.google.com/maps/?q='.$data['rows'][0]['LATITUD'].','.$data['rows'][0]['LONGITUD'] . PHP_EOL;
                break;
            case 'clubs':
                $answer =
                    '👥 Se llama ' . $data['rows'][0]['Nombre'] . PHP_EOL .
                    'está en: ' . $data['rows'][0]['Dirección'] . PHP_EOL .
                    $data['rows'][0]['Código Postal'] . PHP_EOL .
                    'Aquí tienes su teléfono ☎️'.$data['rows'][0]['Teléfono'].', y su email 📧 '.$data['rows'][0]['Email'] . PHP_EOL .
                    'Sus actividades son '.$data['rows'][0]['Actividades'];
                break;
        }

        return $answer . PHP_EOL . PHP_EOL. 'Espero haberte sido de ayuda 😉';
    }

    private function decodeData($callback_data, $entityKey)
    {
        $title = base64_decode(substr($callback_data, strlen($entityKey[0] . '_')));

        return $title;
    }
}