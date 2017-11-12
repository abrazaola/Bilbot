<?php

namespace Bilbot;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;

require_once __DIR__ . '/vendor/autoload.php';

class GetUpdatesDaemon
{
    private $telegram;
    private $online = false;

    public function __construct()
    {
        while (true) {
            if ($this->online) {
                $this->update();
            } else {
                $this->initialize();
            }
        }
    }

    private function initialize()
    {
        try {
            $this->telegram = new Telegram(
                Constants::TELEGRAM_API_KEY,
                Constants::TELEGRAM_API_USERNAME
            );
            $this->telegram->addCommandsPaths([Constants::TELEGRAM_COMMANDS_PATH]);
            $this->telegram->enableAdmins([]);

            try {
                $this->telegram->enableMySql([
                    'host'     => Constants::MYSQL_HOST,
                    'user'     => Constants::MYSQL_USER,
                    'password' => Constants::MYSQL_PASSWORD,
                    'database' => Constants::MYSQL_DATABASE,
                ]);

                $this->online = true;
                echo 'STARTED - Bilbot online.' . PHP_EOL;

            } catch (\Exception $e) {
                return;
            }

            TelegramLog::initErrorLog(__DIR__ . Constants::TELEGRAM_ERROR_LOGS_PATH);
            TelegramLog::initDebugLog(__DIR__ . Constants::TELEGRAM_DEBUG_LOGS_PATH);
            //Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . "/{$bot_username}_update.log");

            $this->telegram->setDownloadPath(__DIR__ . Constants::TELEGRAM_DOWNLOADS_PATH);
            $this->telegram->setUploadPath(__DIR__ . Constants::TELEGRAM_UPLOADS_PATH);

            $this->telegram->enableLimiter();
        } catch (TelegramException $e) {
            echo $e->getMessage();
            TelegramLog::error($e);
        } catch (TelegramLogException $e) {
            echo $e->getMessage();
        }
    }

    private function update()
    {
            try {
                $server_response = $this->telegram->handleGetUpdates();

                if ($server_response->isOk()) {
                    $update_count = count($server_response->getResult());
                    echo date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates';
                } else {
                    echo date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL;
                    echo $server_response->printError();
                }
            } catch (TelegramException $e) {
                echo $e->getMessage();
                TelegramLog::error($e);
            } catch (TelegramLogException $e) {
                echo $e->getMessage();
            }
    }
}