<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Exception;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use ReflectionClass;

/**
 * User "/agenda" command
 *
 * Returns information about Bilbao's events agenda
 */
class AgendaCommand extends UserCommand
{
    const RELEVANCE_THRESHOLD = 0.85;
    const NEGATIVENESS_THRESHOLD = -1;

    protected $name = 'agenda';
    protected $description = 'Información sobre la agenda de eventos de Bilbao';
    protected $usage = '/agenda <texto>';
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
        $incomingMessage = trim($message->getText(true));
        $incomingMessage = str_replace(['?', '!', '+'], '', $incomingMessage);

        $answerMessage = 'Lo siento, pero no he encuentro información relevante. ¿Puedes probar a preguntármelo de otro modo?';
        $agendaKeywords = [
            'euskalduna',
            'guggenheim',
            'conciertos',
            'concierto',
            'eventos',
            'evento',
            'concurso',
            'concursos',
            'taller',
            'talleres'
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

            $clientWatson = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WATSON_API_ENDPOINT]);
            $resWatson = $clientWatson->get(
                'understandme',
                ['query' => ['text' => $incomingMessage]]
            )->getBody()->getContents();
            $resWatson = json_decode($resWatson, true);
            $incomingMessageWords = explode(" ", strtolower($incomingMessage));
            $emotionPrefix = '';

            //var_dump($resWatson);
            //var_dump($incomingMessageWords);

            if (
                $resWatson['analysis']['sentiment']['document']['label'] == 'negative'
            ) {
                $emotionPrefix = '😔 Lo siento, solo soy un bot';
            }

            if (
                $resWatson['analysis']['sentiment']['document']['label'] == 'positive'
            ) {
                $emotionPrefix = '¡Buenas noticias!';
            }


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


            foreach ($agendaKeywords as $keyword) {
                if (in_array($keyword, $incomingMessageWords)) {
                    $clientWelive = new \GuzzleHttp\Client(['base_uri' => \Bilbot\Constants::BILBOT_WELIVE_API_ENDPOINT]);
                    $resWelive = $clientWelive->get(
                        'agenda_search',
                        [
                            'query' => [
                                'term' => $keyword,
                            ]
                        ]
                    )->getBody()->getContents();
                    $resWelive = json_decode($resWelive, true);

                    $answerMessage =
                        $emotionPrefix .
                        'Con respecto a ' .
                        $keyword .
                        ', aquí tienes lo que he encontrado' .
                        PHP_EOL;

                    $answerButtons = [];

                    foreach ($resWelive['rows'] as $row) {
                        $answerButtons[] = [new InlineKeyboardButton([
                            'text' => '📆 ' . $row['titulo'] . ' hasta el ' . $row['fecha_hasta'],
                            'callback_data' => 'agenda_'.$row['titulo']
                        ])];
                    }

                    $reflect  = new ReflectionClass(InlineKeyboard::class);
                    $keyboard = $reflect->newInstanceArgs($answerButtons);

                    //var_dump($keyboard);

                    $data = [
                        'chat_id' => $chat_id,
                        'text' => $answerMessage,
                        'reply_markup' => $keyboard,
                    ];

                    return Request::sendMessage($data);
                }
            }


            $data = [
                'chat_id' => $chat_id,
                'text' => $answerMessage,
            ];

            return Request::sendMessage($data);
        } catch (Exception $e) {
            $answerMessage = 'Necesito una frase más larga: ' . PHP_EOL . json_encode(['error' => $e->getMessage()]);
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $answerMessage,
        ];

        return Request::sendMessage($data);
    }
}