<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Bilbot\CommandsHelper;
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
 * User "/turismo" command
 *
 * Returns information about Bilbao's tourist offices
 */
class TurismoCommand extends UserCommand
{
    protected $name = 'turismo';
    protected $description = 'Información sobre oficinas de turismo de la ciudad';
    protected $usage = '/turismo <texto>';
    protected $version = '0.1.0';

    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;

    const WELIVE_SEARCH_METHOD = 'tourist_offices_search';
    const WELIVE_LIST_METHOD = 'tourist_offices_list';

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'tourist_';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage = str_replace(['¿', '?', '¡', '!', '+'], '', trim($message->getText(true)));
        $incomingMessageWords = explode(" ", strtolower($incomingMessage));
        $fallbackMessage = PhraseRandomizer::getRandomPhrase(Constants::PHRASE_FALLBACK);

        $answerMessage = $fallbackMessage;

        $genericKeywords = [
            'donde',
            'turismo', 'turístico', 'turistico',
            'oficina', 'oficinas',
            'información', 'informacion',
            'conocer',
        ];

        $specificKeywords = [
            'ekintza',
            'bizkaia',
            'guggenheim',
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
        $keyword = CommandsHelper::singularize($keyword);

        if ($withTerm) {
            $resWelive = CommandsHelper::sendToWeLive(self::WELIVE_SEARCH_METHOD, $keyword);
        } else {
            $resWelive = CommandsHelper::sendToWeLive(self::WELIVE_LIST_METHOD);
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
            $answerButtons[] = [new InlineKeyboardButton([
                'text' => 'ℹ️ ' . $row['documentName'],
                'callback_data' => CommandsHelper::encodeData($row['_id'], self::DATA_PREFIX, self::DATA_LENGTH)
            ])];
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
}