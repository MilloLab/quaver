<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Config;

$url = $this->getCurrentRoute();

header('HTTP/1.0 404 Not Found');
trigger_error("[404] $url", E_USER_WARNING);

if (!defined('AJAX_METHOD')) {
    $this->addTwigVars('siteTitle', 'Error 404 - '.Config::get('app.BRAND_NAME'));
    $this->addTwigVars('e404', true);
    $this->setView('http-errors');
    $this->render();
}
