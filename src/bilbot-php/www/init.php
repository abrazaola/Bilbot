<?php
require_once('vendor/autoload.php');

# User defined commands
require_once('Commands/EchoCommand.php');
require_once('Commands/UnderstandMeCommand.php');
require_once('Commands/AgendaCommand.php');
require_once('Commands/BicisCommand.php');
require_once('Commands/AsociacionesCommand.php');
require_once('Commands/HotelesCommand.php');
require_once('Commands/RestaurantesCommand.php');
require_once('Commands/AtraccionesCommand.php');
require_once('Commands/TurismoCommand.php');

require_once('Commands/CallbackqueryCommand.php');
require_once('Constants.php');
require_once('GetUpdatesDaemon.php');

new Bilbot\GetUpdatesDaemon();