<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver;

define('GLOBAL_PATH', dirname(__FILE__));
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
if (!file_exists(GLOBAL_PATH . '/ajax.log')) {
    $logFile = fopen(GLOBAL_PATH . "/ajax.log", "w") or die("Unable to open file!");
    fwrite($logFile, '');
    fclose($logFile);
}
ini_set('error_log', GLOBAL_PATH . '/ajax.log');
ini_set('log_errors', 'On');

date_default_timezone_set("Europe/Madrid");

// Check config file
if (!file_exists(GLOBAL_PATH . '/Quaver/Config.php') || !file_exists(GLOBAL_PATH . '/Quaver/Core/Autoloader.php')) {
    $msg = "This instance of app doesn't seem to be configured, 
    please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
}

// Autoloader & Config
require_once(GLOBAL_PATH . '/Quaver/Config.php');
require_once(GLOBAL_PATH . '/Quaver/Core/Autoloader.php');
require_once(GLOBAL_PATH . '/vendor/autoload.php');
\Twig_Autoloader::register();

use Quaver\Core\Bootstrap;

$bootstrap = new Bootstrap;
$bootstrap->run();
