<?php

namespace Bilbot;


use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use ReflectionClass;

class CommandsHelper
{
    public static function sendToWatson($incomingMessage) {
        $clientWatson = new \GuzzleHttp\Client(['base_uri' => Constants::BILBOT_WATSON_API_ENDPOINT]);
        $resWatson = $clientWatson->get(
            'understandme',
            ['query' => ['text' => $incomingMessage]]
        )->getBody()->getContents();

        $resWatson = json_decode($resWatson, true);

        return $resWatson;
    }

    public static function sendToWeLive($method, $keyword = false) {
        $clientWelive = new \GuzzleHttp\Client(['base_uri' => Constants::BILBOT_WELIVE_API_ENDPOINT]);

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

    public static function getEmotionPrefix($resWatson) {
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

    public static function encodeData($title, $dataPrefix, $dataLength) {
        return $dataPrefix . base64_encode(substr($title, 0, $dataLength));
    }

    public static function singularize($pluralWord) {
        if (substr($pluralWord, -2) == 'es') {
            return rtrim($pluralWord, 'es');
        }

        if (substr($pluralWord, -1) == 's') {
            return rtrim($pluralWord, 's');
        }

        return $pluralWord;
    }

    public static function search(
        $keyword,
        $emotionPrefix,
        $fallbackMessage,
        $chatId,
        $searchMethod,
        $listMethod,
        $dataPrefix,
        $dataLength,
        $emoji,
        $titleKey,
        $dataKey,
        $withTerm = false,
        $titleDescriptionKey = null
    ) {
        $keyword = CommandsHelper::singularize($keyword);

        if ($withTerm) {
            $resWelive = CommandsHelper::sendToWeLive($searchMethod, $keyword);
        } else {
            $resWelive = CommandsHelper::sendToWeLive($listMethod);
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
            if ($titleDescriptionKey == null) {
                $answerButtons[] = [new InlineKeyboardButton([
                    'text' => $emoji . ' ' . $row[$titleKey],
                    'callback_data' => CommandsHelper::encodeData($row[$dataKey], $dataPrefix, $dataLength)
                ])];
            } else {
                $answerButtons[] = [new InlineKeyboardButton([
                    'text' => $emoji . ' ' . $row[$titleKey] . ' (' . $row[$titleDescriptionKey] . ')',
                    'callback_data' => CommandsHelper::encodeData($row[$dataKey], $dataPrefix, $dataLength)
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
}