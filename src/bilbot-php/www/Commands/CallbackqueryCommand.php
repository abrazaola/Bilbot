<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
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
            ],
            'hotels' => [
                'action' => 'hotels_detail',
                'column' => '_id'
            ],
            'restaurants' => [
                'action' => 'restaurants_detail',
                'column' => '_id'
            ],
            'attractions' => [
                'action' => 'tourist_attractions_detail',
                'column' => '_id'
            ],
            'tourist' => [
                'action' => 'tourist_offices_detail',
                'column' => '_id'
            ],
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

        if (isset($resWelive['results'])) {
            $resWelive = $resWelive['results'];
        }

        if ($resWelive['count'] != 1) {
            return Request::sendMessage([
                'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                'text' =>
                    '¡Ups! Ha habido un problema y no puedo mostrarte esta información, ¿puedes probar en otro momento?',
            ]);
        }

        $eventInfoAnswer = $this->buildAnswer($entityKey[0], $resWelive);

        if ($eventInfoAnswer['url'] == '') {
            return Request::sendMessage([
                'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                'text' => $eventInfoAnswer['text'],
                'parse_mode' => 'html'
            ]);
        } else {
            return Request::sendMessage([
                'chat_id' => $callback_query->getMessage()->getChat()->getId(),
                'text' => $eventInfoAnswer['text'],
                'parse_mode' => 'html',
                'reply_markup' => new InlineKeyboard(
                        [
                            new InlineKeyboardButton(
                                ['text' => 'Más información', 'url' => $eventInfoAnswer['url']]
                            )
                        ]
                )
            ]);
        }
    }

    private function buildAnswer($entityType, $data) {
        $answer = '';
        $url = '';

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
            case 'hotels':
                $answer =
                    '🏨 ' . $data['rows'][0]['documentName'] . ' (' . $data['rows'][0]['lodgingType'] . ')' . PHP_EOL .
                    PHP_EOL . $data['rows'][0]['turismDescription'] . PHP_EOL .
                    PHP_EOL . 'Teléfono: ' . $data['rows'][0]['phoneNumber'] . PHP_EOL .
                    'Email: ' . $data['rows'][0]['email'] . PHP_EOL .
                    'Web: ' . $data['rows'][0]['web'] . PHP_EOL;

                    if ($data['rows'][0]['accessibility'] == '1') {
                        $answer .= PHP_EOL .'Además, cuenta con medios accesibles' . PHP_EOL;
                    }

                    if ($data['rows'][0]['qualityQ'] == '1') {
                        $answer .= PHP_EOL .'🏆 Le han otorgado la Q de calidad' . PHP_EOL;
                    }

                    if ($data['rows'][0]['friendlyUrl'] != '') {
                        $url = $data['rows'][0]['friendlyUrl'];
                    }
                break;
            case 'restaurants':
                $answer =
                    '🍽 ' . $data['rows'][0]['restorationType'] . ' ' . $data['rows'][0]['documentName'] . PHP_EOL .
                    PHP_EOL .$data['rows'][0]['documentDescription'] . PHP_EOL .
                    PHP_EOL .'Teléfono: ' . $data['rows'][0]['phoneNumber'] . PHP_EOL .
                    'Email: ' . $data['rows'][0]['email'] . PHP_EOL .
                    'Web: ' . $data['rows'][0]['web'] . PHP_EOL;

                if ($data['rows'][0]['accessibility'] == '1') {
                    $answer .= PHP_EOL .'Además, cuenta con medios accesibles' . PHP_EOL;
                }

                if ($data['rows'][0]['michelinStar'] == '1') {
                    $answer .= PHP_EOL .'🏅 Tiene al menos una estrella Michelín' . PHP_EOL;
                }

                if ($data['rows'][0]['friendlyUrl'] != '') {
                    $url = $data['rows'][0]['friendlyUrl'];
                }
                break;
            case 'attractions':
                $answer =
                    '🗺 ' . $data['rows'][0]['NOMBRE_LUGAR_CAS'] . PHP_EOL .
                    'es de tipo ' . $data['rows'][0]['NOMBRE_FAMILIA'] . PHP_EOL .
                    'y se encuentra en ' . $data['rows'][0]['NOMBRE_TIPO_VIA'] . ' ' . $data['rows'][0]['NOMBRE_CALLE'] . ' ' . $data['rows'][0]['NUMERO'] . ' ' . $data['rows'][0]['BLOQUE'] . PHP_EOL .
                    '📍 Mapa => https://www.google.com/maps/?q='.$data['rows'][0]['COORDENADA_UTM_X'].','.$data['rows'][0]['COORDENADA_UTM_Y'] . PHP_EOL;
                break;
            case 'tourist':
                $answer =
                    'ℹ️ ' . $data['rows'][0]['documentName'] . PHP_EOL .
                    $data['rows'][0]['documentDescription'] . PHP_EOL .
                    'Email: ' . $data['rows'][0]['email'] . PHP_EOL .
                    'su teléfono es: ' . $data['rows'][0]['phoneNumber'] . PHP_EOL .
                    '📍 Mapa => https://www.google.com/maps/?q='.$data['rows'][0]['latitudelongitude'] . PHP_EOL;

                    if ($data['rows'][0]['friendlyUrl'] != '') {
                        $url = $data['rows'][0]['friendlyUrl'];
                    }
                break;
        }

        return [
            'text' => $answer . PHP_EOL . PHP_EOL. 'Espero haberte sido de ayuda 😉',
            'url' => $url
        ];
    }

    private function decodeData($callback_data, $entityKey)
    {
        $title = base64_decode(substr($callback_data, strlen($entityKey[0] . '_')));

        return $title;
    }
}