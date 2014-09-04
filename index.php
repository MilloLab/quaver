<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

ini_set('display_errors', 0);

// Check config file
if ( !file_exists("./app/config.php") ) {

	$msg = "This instance of app doesn't seem to be configured, please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
    
}

// Load configuration
require_once("./app/config.php");

// Check dev mode
if (defined('DEV_MODE')) {       
    if (DEV_MODE) {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
        ini_set('display_errors', 1);
    }
}

// Load other main classes
require_once("./app/core/db.php");
require_once("./app/core/lang.php");
require_once("./app/core/core.php");

// Load YAML Parser
require_once('./app/lib/yaml/vendor/autoload.php');

// Check maintenance 
if (defined(MAINTENANCE_MODE) && MAINTENANCE_MODE === true && $_SERVER['REQUEST_URI'] != '/maintenance') {
    header('Location: /maintenance');
    exit;
}

// Autoload models
spl_autoload_register(

    function ($cls) {

        $className = null;
        // Convert class name to filename format.
        $className = strtolower( $cls );

        if( file_exists( MODEL_PATH."/$className.php" ) ){
            require_once( MODEL_PATH."/$className.php" );
        }
    }

);

// Init core
$core = new core;
$core->start();

?>