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
 * User "/asociaciones" command
 *
 * Returns information about Bilbao's events agenda
 */
class AsociacionesCommand extends UserCommand
{
    protected $name = 'asociaciones';
    protected $description = 'Información sobre las asociaciones existentes en Bilbao';
    protected $usage = '/asociaciones <texto>';
    protected $version = '0.1.0';

    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;

    const WELIVE_SEARCH_METHOD = 'clubs_search';
    const WELIVE_LIST_METHOD = 'clubs_list';

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'clubs_';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage = str_replace(['¿', '?', '¡', '!', '+'], '', trim($message->getText(true)));
        $incomingMessageWords = explode(" ", strtolower($incomingMessage));
        $fallbackMessage = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_FALLBACK);

        $answerMessage = $fallbackMessage;

        $genericKeywords = [
            'asociaciones', 'asociacion',
            'club', 'clubs',
            'hacer',
            'entretenimiento',
        ];

        $specificKeywords = [
            'taller', 'talleres'
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
                        '👥',
                        'Nombre',
                        '_id',
                        true
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
                        '👥',
                        'Nombre',
                        '_id',
                        false
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