<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under Apache License 2.0
 * (see README for details)
 */

ini_set('display_errors','off');
error_reporting(E_ERROR);

if ( !file_exists("./config.php") ) {

	$msg = "This instance of app doesn't seem to be configured, please read the deployment guide, configure and try again.";
    error_log($msg);
    echo "<h1>{$msg}</h1>";
    die;
	
}

// Set main objects
require_once("./config.php");
define('P_PATH', ''); //AJAX
require_once("./app/core/db.php");
require_once("./app/core/lang.php");
require_once("./app/core/core.php");

# Load YAML Parser
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

        $paths = array(
            MODEL_PATH,
            LIB_PATH
        );

        foreach( $paths as $path ) {
            if( file_exists( "$path/$className.php" ) ){
                require_once( "$path/$className.php" );
            }

        }

    }

);

// Init core
$core = new core;
$core->start();

?>