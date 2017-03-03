<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

$url = $this->getCurrentRoute();

header('HTTP/1.0 404 Not Found');
trigger_error("[404] $url", E_USER_WARNING);

if (!defined('AJAX_METHOD')) {
    $this->addTwigVars('siteTitle', 'Error 404 - '.$this->getContainer()->get('config')->val('app.BRAND_NAME'));
    $this->addTwigVars('e404', true);
    $this->setView('http-errors');
    $this->render();
}
