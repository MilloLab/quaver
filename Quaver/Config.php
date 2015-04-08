<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

// Core Path
define('MODEL_PATH', GLOBAL_PATH . '/Quaver/App/Model');
define('CONTROLLER_PATH', GLOBAL_PATH . '/Quaver/App/Controller');

// Resource Path
define('FILES_PATH', './files');
define('THEME_QUAVER', 'Default');
define('BRAND_NAME', 'My Company Name');

// Internal definition
define('QV_ROUTES_EXT', 0);
define('QV_ROUTES_EXT_PATH', ''); //name of package

// Cookies
define('COOKIE_NAME', 'quaver');
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);
define('COOKIE_PATH', '/');

// Modes
define('HTTP_MODE', 'http://');
define('MAINTENANCE_MODE', false);
define('DEV_MODE', true);

// Random variable to front files
define('RANDOM_VAR', '20141214'); // format YYYYMMDD

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
define('DB_PORT', '3306');

/*
 * External
 */

// Enabled mailing model
define('MAIL_ENABLED', true);

// Contact mail
define('CONTACT_EMAIL', 'info@mydomain.com');
define('CONTACT_NAME', 'My Company Name');

// MANDRILL
define('MANDRILL', false);
define('MANDRILL_USERNAME', '');
define('MANDRILL_APIKEY', '');

// Cypher if you want use
define('CIPHER_KEY', '');
