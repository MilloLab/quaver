<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Controller;

use Quaver\Core\Controller;
use Quaver\Core\Lang;
use Quaver\Core\Config;

/**
 * Home controller (language, maintenance and index).
 */
class home extends Controller
{
    /**
     * Show homepage.
     *
     * @return mixed
     */
    public function homeAction()
    {
        $this->render();
    }

    /**
     * Show maintenance page.
     *
     * @return mixed
     */
    public function maintenanceAction()
    {   
        $config = Config::getInstance();
        if ($config->params->core['maintenance']) {
            $this->render();
            exit;
        } else {
            header('Location: /');
            exit;
        }
    }

    /**
     * Change language.
     *
     * @return mixed
     */
    public function languageAction()
    {
        $language = new Lang();
        $language->getFromSlug($this->router->getUrlPart(0));

        if ($language) {
            $language->setCookie();
            if (!empty($_SERVER['HTTP_REFERER'])) {
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit;
            } else {
                header('Location: /');
                exit;
            }
        } else {
            $this->router->dispatch('e404');
            exit;
        }
    }
}
