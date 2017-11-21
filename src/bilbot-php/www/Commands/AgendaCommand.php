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
    const NEGATIVENESS_THRESHOLD = -0.9;
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
        $text    = trim($message->getText(true));
        $answer = '...';

        if ($text === '') {
            $answer = 'Uso del comando: ' . $this->getUsage();
        } else {
            try {
                $client = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);

                $res = $client->get(
                    'understandme',
                    ['query' => ['text' => $text]]
                )->getBody()->getContents();

                $res = json_decode($res, true);
                var_dump($res);
                if (
                    $res['analysis']['sentiment']['document']['label'] == 'positive' ||
                    $res['analysis']['sentiment']['document']['label'] == 'neutral'
                ) {
                    //$answer = 'Estás hablando sobre ' . http_build_query($res['analysis']['concepts']);

                    foreach ($res['analysis']['concepts'] as $concept) {
                        if ($concept['text'] == 'Fin de semana' && $concept['relevance'] > self::RELEVANCE_THRESHOLD) {
                            $answer = 'Estás hablando sobre ' . $concept['text'];
                        }

                        if ($concept['text'] == 'Semana laboral' && $concept['relevance'] > self::RELEVANCE_THRESHOLD) {
                            $answer = 'Estás hablando sobre ' . $concept['text'];
                        }

                        if ($concept['text'] == 'Tarde' && $concept['relevance'] > self::RELEVANCE_THRESHOLD) {
                            $answer = 'Estás hablando sobre ' . $concept['text'];
                        }
                    }
                }

                if (count($res['analysis']['concepts']) == 0) {
                    $answer = 'Lo siento, creo que no comprendo, ¿Puedes intentarlo de nuevo?';
                }

                if (
                    $res['analysis']['sentiment']['document']['label'] == 'negative' &&
                    $res['analysis']['sentiment']['document']['score'] < self::NEGATIVENESS_THRESHOLD
                ) {
                    $answer = 'Lo siento, solo soy un bot que hace lo que puede...';
                }
            } catch (Exception $e) {
                $answer = 'Puede que necesite frases más largas: '.PHP_EOL.json_encode(['error' => $e->getMessage()]);
            }
        }


        $data = [
            'chat_id' => $chat_id,
            'text'    => $answer,
        ];

        return Request::sendMessage($data);
    }
}