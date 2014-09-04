<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

// Paths
define('GLOBAL_PATH', dirname( __FILE__ ));
define('MODEL_PATH', GLOBAL_PATH . '/model');
define('VIEW_PATH', GLOBAL_PATH . '/view');
define('CONTROLLER_PATH', GLOBAL_PATH . '/controller');
define('LIB_PATH', GLOBAL_PATH . '/lib');

// Resources path
define('CSS_PATH', '/app/resources/css');
define('JS_PATH',  '/app/resources/js');
define('IMG_PATH',  '/app/resources/img');

// Cookies
define('COOKIE_NAME', 'yourdomain');
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
define('COOKIE_PATH', '/');

// Modes
define('HTTP_MODE', 'http://');
define('MAINTENANCE_MODE', false);
define('DEV_MODE', true);

// Random variable to FrontEnd files
define('RANDOM_VAR', ''); // format YYYYMMDD

// Template cache (Twig)
define('TEMPLATE_CACHE', false);

// Auto reload cache (Twig)
define('CACHE_AUTO_RELOAD', true);

// Default Language
define('LANG', 1);

// Force language
define('LANG_FORCE', false);

// Database configuration
define('DB_HOSTNAME',  'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'qv');

/*
 * Global variables DO NOT TOUCH
 */
$_user = '';
$_language = '';


/*
 * External
 */

// MANDRILL
define('MANDRILL', false);
define('MANDRILL_USERNAME', '');
define('MANDRILL_APIKEY', '');

// Contact mail
define('CONTACT_EMAIL', '');
define('CONTACT_NAME', '');

/*
 * Cypher
 *
 * /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
 * /!\ /!\ /!\ /!\ /!\ WARNING /!\ /!\ /!\ /!\ /!\ /!\ /!\
 * /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
 * PLEASE DON'T CHANGE OR DELETE
 *
 */
define('CIPHER_KEY', 'yourcipherkey'); //RC4

?>