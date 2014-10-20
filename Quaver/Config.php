<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

// Core Path
define('GLOBAL_PATH', dirname( __FILE__ ));
define('MODEL_PATH', GLOBAL_PATH . '/Model');
define('VIEW_PATH', GLOBAL_PATH . '/View');
define('CONTROLLER_PATH', GLOBAL_PATH . '/Controller');
define('LIB_PATH', GLOBAL_PATH . '/Lib');

// Resource Path
define('FILES_PATH', GLOBAL_PATH . '/Files');
define('RES_PATH', GLOBAL_PATH . '/Resources'); #Master
define('CSS_PATH', RES_PATH . '/css');
define('JS_PATH',  RES_PATH . '/js');
define('IMG_PATH', RES_PATH . '/img');

// Cookies
define('COOKIE_NAME', 'yourdomain');
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
define('COOKIE_PATH', '/');

// Modes
define('HTTP_MODE', 'http://');
define('MAINTENANCE_MODE', false);
define('DEV_MODE', true);

// Random variable to front files
define('RANDOM_VAR', ''); // format YYYYMMDD

// Template cache, manual clean (Twig)
define('TEMPLATE_CACHE', false);

// Auto reload cache (Twig)
define('CACHE_AUTO_RELOAD', true);

// Default Language
define('LANG', 1);
define('LANG_FORCE', false);

// Database configuration
define('DB_HOSTNAME',  'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'qv');

/*
 * External
 */

// Contact mail
define('CONTACT_EMAIL', '');
define('CONTACT_NAME', '');

// MANDRILL
define('MANDRILL', false);
define('MANDRILL_USERNAME', '');
define('MANDRILL_APIKEY', '');

// Cypher if you want use
define('CIPHER_KEY', 'yourcipherkey'); //RC4

?>
