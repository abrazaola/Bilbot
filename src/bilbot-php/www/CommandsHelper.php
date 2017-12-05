<?php

namespace Bilbot;


class CommandsHelper
{
    public static function sendToWatson($incomingMessage) {
        $clientWatson = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);
        $resWatson = $clientWatson->get(
            'understandme',
            ['query' => ['text' => $incomingMessage]]
        )->getBody()->getContents();

        $resWatson = json_decode($resWatson, true);

        return $resWatson;
    }

    public static function sendToWeLive($method, $keyword = false) {
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
}