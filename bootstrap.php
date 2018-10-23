<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver;

define('GLOBAL_PATH', dirname(__FILE__));

chdir(GLOBAL_PATH);

if (defined('LOG_FILENAME') && LOG_FILENAME === 'cli.log') {
    ini_set('display_errors', 0);
} else {
    ini_set('display_errors', 1);
}

error_reporting(E_ALL | E_STRICT);

$logFile = defined('LOG_FILENAME') ? LOG_FILENAME : 'script_errors.log';
if (!file_exists(GLOBAL_PATH.'/logs/'.$logFile)) {
    $logFile = fopen(GLOBAL_PATH.'/logs/'.$logFile, 'w') or die('Unable to open file!');
    fwrite($logFile, '');
    fclose($logFile);
}
ini_set('error_log', GLOBAL_PATH.'/logs/'.$logFile);
ini_set('log_errors', 'On');

date_default_timezone_set('Europe/Madrid');

// Check config file
if (php_sapi_name() !== 'cli' && !file_exists(GLOBAL_PATH.'/App/Config.yml')) {
    $msg = "This instance of app doesn't seem to be configured,
    please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
}

require_once GLOBAL_PATH.'/vendor/autoload.php';
