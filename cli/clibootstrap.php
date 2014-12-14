<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

date_default_timezone_set("Europe/Madrid");

ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
ini_set('error_log','script_errors.log');
ini_set('log_errors','On');

define('GLOBAL_PATH', dirname( __FILE__ ));
define('MODEL_PATH', GLOBAL_PATH . '/../Quaver/Model');
define('VIEW_PATH', GLOBAL_PATH . '/../Quaver/View');
define('CONTROLLER_PATH', GLOBAL_PATH . '/../Quaver/Controller');


