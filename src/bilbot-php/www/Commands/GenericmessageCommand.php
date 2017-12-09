<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Bilbot\CommandsHelper;
use Bilbot\Constants;
use Bilbot\PhraseRandomizer;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use ReflectionClass;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.1.0';
    protected $need_mysql = true;

    const DATA_LENGTH = 24;
    const DATA_PREFIX = 'bikes_';
    const WELIVE_LOCATION_METHOD = 'bikes_near_point';

    public function executeNoDb()
    {
        //Do nothing
        return Request::emptyResponse();
    }

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        //Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        if ($this->getMessage()->getLocation() != null) {
            $chatId = $this->getMessage()->getChat()->getId();

            Request::sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);

            $location = [
                'coordY' => $this->getMessage()->getLocation()->getLatitude(),
                'coordX' => $this->getMessage()->getLocation()->getLongitude()
            ];

            $data = $this->searchBikesWithLocation($chatId, $location);

            return Request::sendMessage($data);
        }

        return Request::emptyResponse();
    }

    private function searchBikesWithLocation(
        $chatId,
        $location
    ) {
        $resWelive = CommandsHelper::sendToWeLive(self::WELIVE_LOCATION_METHOD, false, $location);

        if (count($resWelive) == 0) {
            $data = [
                'chat_id' => $chatId,
                'text' => PhraseRandomizer::getRandomPhrase(Constants::PHRASE_NOT_FOUND_WITH_LOCATION),
            ];

            return $data;
        }

        $answerMessage =
            PhraseRandomizer::getRandomPhrase(Constants::PHRASE_FOUND_WITH_LOCATION) .
            PHP_EOL;

        $answerButtons = [];

        foreach ($resWelive as $row) {
            $answerButtons[] = [new InlineKeyboardButton([
                'text' => '📍 ' . $row['NOMBRE'],
                'callback_data' => CommandsHelper::encodeData(
                    $row['_id'],
                    self::DATA_PREFIX,
                    self::DATA_LENGTH
                )
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
