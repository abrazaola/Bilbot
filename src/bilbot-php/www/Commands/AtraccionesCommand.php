<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Bilbot\Constants;
use Bilbot\PhraseRandomizer;
use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use ReflectionClass;

/**
 * User "/atracciones" command
 *
 * Returns information about Bilbao's tourist attractions
 */
class AtraccionesCommand extends UserCommand
{
    protected $name = 'atracciones';
    protected $description = 'Información sobre atracciones turísticas de la ciudad';
    protected $usage = '/atracciones <texto>';
    protected $version = '0.1.0';

    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;

    const WELIVE_SEARCH_METHOD = 'tourist_attractions_search';
    const WELIVE_LIST_METHOD = 'tourist_attractions_list';

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'attractions_';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage = str_replace(['¿', '?', '¡', '!', '+'], '', trim($message->getText(true)));
        $incomingMessageWords = explode(" ", strtolower($incomingMessage));
        $fallbackMessage = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_FALLBACK);

        $answerMessage = $fallbackMessage;

        $genericKeywords = [
            'ver',
            'visitar',
            'turismo',
            'conocer',
        ];

        $specificKeywords = [
            'museo', 'museos',
            'edificio', 'edificios',
            'arquitectura',
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
            $answerMessage = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_LONGER);
            TelegramLog::error($e);
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

            return $data;
        }

        $answerMessage =
            $emotionPrefix .
            PhraseRandomizer::getRandomPhrase(Constants::PHRASE_RESULTS_FOUND) .
            PHP_EOL;

        if ($withTerm) {
            $answerMessage =
                $emotionPrefix .
                PhraseRandomizer::getRandomPhrase(Constants::PHRASE_RESULTS_SPECIFIC_CONNECTOR) .
                $keyword .
                PhraseRandomizer::getRandomPhrase(Constants::PHRASE_RESULTS_SPECIFIC_FOUND) .
                PHP_EOL;
        }

        $answerButtons = [];

        foreach ($resWelive['rows'] as $row) {
            if ($row['NOMBRE_LUGAR_CAS'] != '') {
                $answerButtons[] = [new InlineKeyboardButton([
                    'text' => '🗺 ' . $row['NOMBRE_LUGAR_CAS'],
                    'callback_data' => $this->encodeData($row['_id'])
                ])];
            }
        }

        $reflect = new ReflectionClass(InlineKeyboard::class);
        $keyboard = $reflect->newInstanceArgs($answerButtons);

        $data = [
            'chat_id' => $chatId,
            'text' => $answerMessage,
            'reply_markup' => $keyboard,
        ];

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
        $emotionPrefix = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_EMOTION_NEUTRAL);

        if (
            $resWatson['analysis']['sentiment']['document']['label'] == 'negative'
        ) {
            $emotionPrefix = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_EMOTION_NEGATIVE);
        }

        if (
            $resWatson['analysis']['sentiment']['document']['label'] == 'positive'
        ) {
            $emotionPrefix = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_EMOTION_POSITIVE);
        }

        return $emotionPrefix;
    }

    private function encodeData($title)
    {
        return self::DATA_PREFIX . base64_encode(substr($title, 0, self::DATA_LENGTH));
    }
}