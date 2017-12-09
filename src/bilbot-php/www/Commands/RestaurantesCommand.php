<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Bilbot\CommandsHelper;
use Bilbot\Constants;
use Bilbot\PhraseRandomizer;
use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

/**
 * User "/restaurantes" command
 *
 * Returns information about Bilbao's restaurants
 */
class RestaurantesCommand extends UserCommand
{
    protected $name = 'restaurantes';
    protected $description = 'Información sobre restaurantes de la ciudad';
    protected $usage = '/restaurantes <texto>';
    protected $version = '0.1.0';

    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;

    const WELIVE_SEARCH_METHOD = 'restaurants_search';
    const WELIVE_LIST_METHOD = 'restaurants_list';

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'restaurants_';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage = str_replace(['¿', '?', '¡', '!', '+'], '', trim($message->getText(true)));
        $incomingMessageWords = explode(" ", strtolower($incomingMessage));
        $fallbackMessage = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_FALLBACK);

        $answerMessage = $fallbackMessage;

        $genericKeywords = [
            'comer', 'comida', 'comidas',
            'cenar', 'cena', 'cenas',
        ];

        $specificKeywords = [
            'restaurante', 'restaurantes',
            'sidrería', 'sidrerías', 'sidreria', 'sidrerias',
            'asador', 'asadores',
            'sidra', 'vino',
            'cervecera', 'cervecería', 'cerveceria',
            'cerveza',
            'chuletón', 'chuleton'
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
            $resWatson = CommandsHelper::sendToWatson($incomingMessage);
            $emotionPrefix = CommandsHelper::getEmotionPrefix($resWatson);


            foreach ($specificKeywords as $keyword) {
                if (in_array($keyword, $incomingMessageWords)) {
                    Request::sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);
                    $data = CommandsHelper::search(
                        $keyword,
                        $emotionPrefix,
                        $fallbackMessage,
                        $chat_id,
                        self::WELIVE_SEARCH_METHOD,
                        self::WELIVE_LIST_METHOD,
                        self::DATA_PREFIX,
                        self::DATA_LENGTH,
                        '🍽',
                        'documentName',
                        '_id',
                        true,
                        'restorationType'
                    );

                    return Request::sendMessage($data);
                }
            }

            foreach ($genericKeywords as $keyword) {
                if (in_array($keyword, $incomingMessageWords)) {
                    Request::sendChatAction(['chat_id' => $chat_id, 'action' => 'typing']);
                    $data = CommandsHelper::search(
                        $keyword,
                        $emotionPrefix,
                        $fallbackMessage,
                        $chat_id,
                        self::WELIVE_SEARCH_METHOD,
                        self::WELIVE_LIST_METHOD,
                        self::DATA_PREFIX,
                        self::DATA_LENGTH,
                        '🍽',
                        'documentName',
                        '_id',
                        false,
                        'restorationType'
                    );

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
}