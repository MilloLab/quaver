<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Config;

$url = $this->getCurrentRoute();

header('HTTP/1.0 408 Request Timeout');
trigger_error("[408] $url", E_USER_WARNING);

if (!defined('AJAX_METHOD')) {
    $this->addTwigVars('siteTitle', $_lang->l('title-408').' - '.Config::get('app.BRAND_NAME'));
    $this->addTwigVars('e408', true);
    $this->setView('http-errors');
    $this->render();
}
