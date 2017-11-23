<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/agenda" command
 *
 * Returns information about Bilbao's events agenda
 */
class AgendaCommand extends UserCommand
{
    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;
    /**
     * @var string
     */
    protected $name = 'agenda';

    /**
     * @var string
     */
    protected $description = 'Información sobre la agenda de eventos de Bilbao';

    /**
     * @var string
     */
    protected $usage = '/agenda <texto>';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $incomingMessage    = trim($message->getText(true));
        $answerMessage = 'Lo siento, pero no he encuentro información relevante. ¿Puedes probar a preguntármelo de otro modo?';
        $agendaKeywords = [
            'euskalduna',
            'guggenheim',
            'conciertos',
            'concierto',
            'eventos',
            'evento',
        ];

        if ($incomingMessage === '') {
            $answerMessage = 'Uso del comando: ' . $this->getUsage();

            $data = [
                'chat_id' => $chat_id,
                'text'    => $answerMessage,
            ];

            return Request::sendMessage($data);
        }

        try {
            $clientWatson = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);
            $resWatson = $clientWatson->get(
                'understandme',
                ['query' => ['text' => $incomingMessage]]
            )->getBody()->getContents();
            $resWatson = json_decode($resWatson, true);
            $incomingMessageWords = explode(" ", strtolower($incomingMessage));;

            var_dump($resWatson);
            var_dump($incomingMessageWords);

            if (
                $resWatson['analysis']['sentiment']['document']['label'] == 'negative'
            ) {
                $answerMessage = 'Lo siento, solo soy un bot que hace lo que puede...';

                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $answerMessage,
                ];

                return Request::sendMessage($data);
            }

            if (
                $resWatson['analysis']['sentiment']['document']['label'] == 'positive' ||
                $resWatson['analysis']['sentiment']['document']['label'] == 'neutral'
            ) {
                $answerMessage = 'Estás hablando sobre ' . http_build_query($resWatson['analysis']['concepts']);
/*
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
*/
                foreach ($agendaKeywords as $keyword) {
                    if (in_array($keyword, $incomingMessageWords)) {
                        $clientWatson = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WELIVE_API_ENDPOINT]);
                        $resWelive = $clientWatson->get(
                            'agenda_week'
                        )->getBody()->getContents();
                        $resWelive = json_decode($resWelive, true);
                        //var_dump($resWelive);

                        $answerMessage = 'Con respecto a '.$keyword.', aquí tienes la agenda para esta semana'.PHP_EOL;
                        foreach ($resWelive['rows'] as $row) {
                            $answerMessage .= ' - '.$row['titulo'].' en '.$row['lugar'].' hasta el '.$row['fecha_hasta'].PHP_EOL;
                        }

                        break;
                    }
                }

                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $answerMessage,
                ];

                return Request::sendMessage($data);
            }
        } catch (Exception $e) {
            $answerMessage = 'Puede que necesite frases más largas: '.PHP_EOL.json_encode(['error' => $e->getMessage()]);
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $answerMessage,
        ];

        return Request::sendMessage($data);
    }
}