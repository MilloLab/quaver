<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Config;

$url = $this->getCurrentRoute();

header('HTTP/1.0 500 Internal Server Error');
trigger_error("[500] $url", E_USER_ERROR);

if (!defined('AJAX_METHOD')) {
    $this->addTwigVars('siteTitle', 'Error 500 - '.Config::get('app.BRAND_NAME'));
    $this->addTwigVars('e500', true);
    $this->setView('http-errors');
    $this->render();
}
