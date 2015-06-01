<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver;

define('GLOBAL_PATH', dirname(__FILE__));
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
if (!file_exists(GLOBAL_PATH.'/script_errors.log')) {
    $logFile = fopen('script_errors.log', 'w') or die('Unable to open file!');
    fwrite($logFile, '');
    fclose($logFile);
}
ini_set('error_log', 'script_errors.log');
ini_set('log_errors', 'On');

date_default_timezone_set('Europe/Madrid');

// Check config file
if (!file_exists(GLOBAL_PATH.'/Quaver/Config.php') || !file_exists(GLOBAL_PATH.'/Quaver/Core/Autoloader.php')) {
    $msg = "This instance of app doesn't seem to be configured,
    please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
}

// Autoloader & Config
require_once GLOBAL_PATH.'/Quaver/Config.php';
require_once GLOBAL_PATH.'/Quaver/Core/Autoloader.php';
require_once VENDOR_PATH.'/autoload.php';
\Twig_Autoloader::register();

use Quaver\Core\Bootstrap;
use Quaver\Core\Router;

$bootstrap = new Bootstrap();
$router = new Router();

// Add paths
$router->addPath('/', GLOBAL_PATH.'/Quaver/Routes.yml');

// Add modules
$router->addModule('HelloWorld', 'millolab/quaver-helloworld');
$router->addModule('Mail', 'millolab/quaver-mail');

// DevMode
if (defined('DEV_MODE') && DEV_MODE) {
    $router->startBenchProcess(); //false argument to stop under dev mode
}

$bootstrap->router = $router;
$bootstrap->run();
