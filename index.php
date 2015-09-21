<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver;

define('GLOBAL_PATH', dirname(__FILE__));
define('VENDOR_PATH', GLOBAL_PATH.'/vendor');

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
if (!file_exists(GLOBAL_PATH.'/Quaver/Config.yml')) {
    $msg = "This instance of app doesn't seem to be configured,
    please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
}

// Autoloader
require_once GLOBAL_PATH.'/Quaver/Core/Autoloader.php';

// Set template system
require_once VENDOR_PATH.'/autoload.php';
\Twig_Autoloader::register();

// Set bootstrap
use Quaver\Core\Bootstrap;
use Quaver\Core\Router;
use Quaver\Core\Config;

$bootstrap = new Bootstrap();
$router = new Router();

// Start config
$config = Config::getInstance();
$config->setEnvironment();
if ($config->params->core['devMode'] && $config->params->core['benchMark']) {
    $router->startBenchProcess(); //false argument to stop
}

$router->addPath('/', GLOBAL_PATH.'/Quaver/Routes.yml');

// Start plugins
$config->setPluginsYML($router->modules);
// Load routes of module
foreach ($config->plugins as $key => $plugin) {
    if ($plugin['enabled'] && $plugin['params']['useRoutes']) {
        //!empty($modulePath) ? $this->addPath($moduleRoute, $modulePath.'/'.$packageName.'/'.$namespacePath.'/'.'Routes.yml', true) : $this->addPath($moduleRoute, VENDOR_PATH.'/'.$packageName.'/'.$namespacePath.'/'.'Routes.yml', true);
        $router->addPath('/', VENDOR_PATH.'/'.$plugin['packageName'].'/'.$plugin['namespacePath'].'/'.'Routes.yml', true);
    }
}

// Set router and run!
$bootstrap->router = $router;
$bootstrap->run();
