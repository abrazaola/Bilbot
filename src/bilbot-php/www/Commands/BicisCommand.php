<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use ReflectionClass;

/**
 * User "/bicis" command
 *
 * Returns information about Bilbao's bike rental service
 */
class BicisCommand extends UserCommand
{
    protected $name = 'bicis';
    protected $description = 'Información sobre los puntos de recogida de bicicletas';
    protected $usage = '/bicis <texto>';
    protected $version = '0.1.0';

    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;

    const WELIVE_SEARCH_METHOD = 'bikes_search';
    const WELIVE_LIST_METHOD = 'bikes_list';

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'bikes_';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage = str_replace(['¿', '?', '¡', '!', '+'], '', trim($message->getText(true)));
        $incomingMessageWords = explode(" ", strtolower($incomingMessage));
        $fallbackMessage = '😣 Lo siento, pero no he encuentro información relevante. ¿Puedes probar a preguntármelo de otro modo?';

        $answerMessage = $fallbackMessage;

        $genericKeywords = [
            'bici','bicis',
            'bicicleta', 'bicicletas',
            'recoger', 'recojemos', 'recojo', 'recogida',
            'encontrar',
            'punto', 'puntos',
            'alquilar',
            'cerca'
        ];

        $specificKeywords = [
            'levante',
            'abando',
            'arriaga',
            'rekalde',
            'corazon', 'corazón',
            'indautxu',
            'bolueta',
        ];

        if ($incomingMessage === '') {
            $answerMessage = 'Uso del comando: ' . $this->getUsage();

            $data = [
                'chat_id' => $chat_id,
                'text' => $answerMessage,
            ];

            return Request::sendMessage($data);
        }

        try {
            Request::sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);
            $resWatson = $this->sendToWatson($incomingMessage);
            $emotionPrefix = $this->getEmotionPrefix($resWatson);

            foreach ($resWatson['analysis']['concepts'] as $concept) {
                if ($concept['text'] == 'Fin de semana' && $concept['relevance'] > self::RELEVANCE_THRESHOLD) {
                    $answerMessage = 'Estás hablando sobre ' . $concept['text'];
                    break;
                }

                if ($concept['text'] == 'Semana laboral' && $concept['relevance'] > self::RELEVANCE_THRESHOLD) {
                    $answerMessage = 'Estás hablando sobre ' . $concept['text'];
                    break;
                }

                if ($concept['text'] == 'Tarde' && $concept['relevance'] > self::RELEVANCE_THRESHOLD) {
                    $answerMessage = 'Estás hablando sobre ' . $concept['text'];
                    break;
                }
            }

            foreach ($specificKeywords as $keyword) {
                if (in_array($keyword, $incomingMessageWords)) {
                    Request::sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);
                    $data = $this->search($keyword, $emotionPrefix, $fallbackMessage, $chat_id, true);

                    return Request::sendMessage($data);
                }
            }

            foreach ($genericKeywords as $keyword) {
                if (in_array($keyword, $incomingMessageWords)) {
                    Request::sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);
                    $data = $this->search($keyword, $emotionPrefix, $fallbackMessage, $chat_id, false);

                    return Request::sendMessage($data);
                }
            }

            $data = [
                'chat_id' => $chat_id,
                'text' => $answerMessage,
            ];

            return Request::sendMessage($data);
        } catch (Exception $e) {
            $answerMessage = '😕 Necesito una frase más larga: ' . PHP_EOL . json_encode(['error' => $e->getMessage()]);
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $answerMessage,
        ];

        return Request::sendMessage($data);
    }

    private function search($keyword, $emotionPrefix, $fallbackMessage, $chatId, $withTerm = false)
    {
        if ($withTerm) {
            $resWelive = $this->sendToWeLive(self::WELIVE_SEARCH_METHOD, $keyword);
        } else {
            $resWelive = $this->sendToWeLive(self::WELIVE_LIST_METHOD);
        }

        if ($resWelive['count'] == 0) {
            $data = [
                'chat_id' => $chatId,
                'text' => $fallbackMessage,
            ];
        } else {
            $answerMessage =
                $emotionPrefix .
                'Con respecto a ' .
                $keyword .
                ', aquí tienes lo que he encontrado' .
                PHP_EOL;

            $answerButtons = [];

            foreach ($resWelive['rows'] as $row) {
                $answerButtons[] = [new InlineKeyboardButton([
                    'text' => '📍 ' . $row['NOMBRE'],
                    'callback_data' => $this->encodeData($row['NOMBRE'])
                ])];
            }

            $reflect = new ReflectionClass(InlineKeyboard::class);
            $keyboard = $reflect->newInstanceArgs($answerButtons);

            $data = [
                'chat_id' => $chatId,
                'text' => $answerMessage,
                'reply_markup' => $keyboard,
            ];
        }

        return $data;
    }

    private function sendToWatson($incomingMessage)
    {
        $clientWatson = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);
        $resWatson = $clientWatson->get(
            'understandme',
            ['query' => ['text' => $incomingMessage]]
        )->getBody()->getContents();

        $resWatson = json_decode($resWatson, true);

        return $resWatson;
    }

    private function sendToWeLive($method, $keyword = false)
    {
        $clientWelive = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WELIVE_API_ENDPOINT]);

        if ($keyword == false) {
            $resWelive = $clientWelive->get(
                $method
            )->getBody()->getContents();
        } else {
            $resWelive = $clientWelive->get(
                $method,
                [
                    'query' => [
                        'term' => $keyword,
                    ]
                ]
            )->getBody()->getContents();
        }

        $resWelive = json_decode($resWelive, true);

        return $resWelive;
    }

    private function getEmotionPrefix($resWatson)
    {
        $emotionPrefix = '';

        if (
            $resWatson['analysis']['sentiment']['document']['label'] == 'negative'
        ) {
            $emotionPrefix = '😔 Lo siento, solo soy un bot... ';
        }

        if (
            $resWatson['analysis']['sentiment']['document']['label'] == 'positive'
        ) {
            $emotionPrefix = '😃 ¡Buenas noticias! ';
        }

        return $emotionPrefix;
    }

    private function encodeData($title)
    {
        return self::DATA_PREFIX . base64_encode(substr($title, 0, self::DATA_LENGTH));
    }
}