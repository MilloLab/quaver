<?php

define('GLOBAL_PATH', dirname(__FILE__));
define('VENDOR_PATH', GLOBAL_PATH.'/../vendor');

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

if (!defined('AJAX_METHOD')) {
    $logFile = 'ajax.log';
} else {
    $logFile = 'script_errors.log';
}

if (!file_exists(GLOBAL_PATH.'/../'.$logFile)) {
    $logFile = fopen(GLOBAL_PATH.'/../'.$logFile, 'w') or die('Unable to open file!');
    fwrite($logFile, '');
    fclose($logFile);
}
ini_set('error_log', GLOBAL_PATH.'/../'.$logFile);
ini_set('log_errors', 'On');

date_default_timezone_set('Europe/Madrid');

// Check config file
if (!file_exists(GLOBAL_PATH.'/../Config.yml')) {
    $msg = "This instance of app doesn't seem to be configured,
    please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
}

// Autoloaders
require_once GLOBAL_PATH.'/../Quaver/Core/Autoloader.php';
require_once VENDOR_PATH.'/autoload.php';
\Twig_Autoloader::register();
