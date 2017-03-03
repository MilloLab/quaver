<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

$url = $this->getCurrentRoute();

header('HTTP/1.0 500 Internal Server Error');
trigger_error("[500] $url", E_USER_ERROR);

if (!defined('AJAX_METHOD')) {
    $this->addTwigVars('siteTitle', 'Error 500 - '.$this->getContainer()->get('config')->val('app.BRAND_NAME'));
    $this->addTwigVars('e500', true);
    $this->setView('http-errors');
    $this->render();
}
