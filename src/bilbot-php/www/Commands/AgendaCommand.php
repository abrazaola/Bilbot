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
 * User "/agenda" command
 *
 * Returns information about Bilbao's events agenda
 */
class AgendaCommand extends UserCommand
{
    protected $name = 'agenda';
    protected $description = 'Información sobre la agenda de eventos de Bilbao';
    protected $usage = '/agenda <texto>';
    protected $version = '0.1.0';

    const WELIVE_SEARCH_METHOD = 'agenda_search';
    const WELIVE_LIST_METHOD = 'agenda_list';

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'agenda_';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage = str_replace(['¿', '?', '¡', '!', '+'], '', trim($message->getText(true)));
        $incomingMessageWords = explode(" ", strtolower($incomingMessage));
        $fallbackMessage = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_FALLBACK);

        $answerMessage = $fallbackMessage;

        $genericKeywords = [
            'eventos','evento',
            'actividad', 'actividades',
            'bilbao'
        ];

        $specificKeywords = [
            'euskalduna',
            'guggenheim',
            'arena',
            'perros',
            'basket', 'baloncesto',
            'futbol', 'football',
            'san mamés', 'san mames',
            'animales',
            'arte',
            'concierto', 'conciertos',
            'concurso', 'concursos',
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
                        '📆',
                        'titulo',
                        'titulo',
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
                        '📆',
                        'titulo',
                        'titulo',
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