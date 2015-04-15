<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

date_default_timezone_set("Europe/Madrid");

define('GLOBAL_PATH', dirname( __FILE__ ));
define('MODEL_PATH', GLOBAL_PATH . '/../Quaver/App/Model');
define('CONTROLLER_PATH', GLOBAL_PATH . '/../Quaver/App/Controller');

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
if (!file_exists(GLOBAL_PATH . '/cli.log')) {
    $logFile = fopen(GLOBAL_PATH . "/cli.log", "w") or die("Unable to open file!");
    fwrite($logFile, '');
    fclose($logFile);
}
ini_set('error_log', GLOBAL_PATH . '/cli.log');
ini_set('log_errors', 'On');



