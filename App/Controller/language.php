<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\App\Model\Lang;

$language = new Lang();
$language = $language->getFromSlug($this->getUrlPart(0));

if ($language && $language->exists()) {
    $language->setCookie();
    $user = $this->getContainer()->get('user');
    if ($user->isLogged()) {
        $user->language = $language->id;
        $user->save();
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
