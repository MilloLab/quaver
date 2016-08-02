<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Model\Lang;

$language = new Lang();
$language->getFromSlug($this->getUrlPart(0));

if ($language) {
    $language->setCookie();
    if ($_user->isLogged()) {
        $_user->language = $language->id;
        $_user->save();
    }
    if (!empty($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_HOST'] === parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) {
        $this->redirect($_SERVER['HTTP_REFERER']);
    } else {
        $this->redirect('/');
    }
} else {
    $this->router->dispatch('e404');
    exit;
}
