<?php
require_once('vendor/autoload.php');
require_once('Commands/EchoCommand.php');
require_once('Commands/UnderstandMeCommand.php');
require_once('Commands/AgendaCommand.php');
require_once('Constants.php');
require_once('GetUpdatesDaemon.php');

new Bilbot\GetUpdatesDaemon();