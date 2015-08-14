<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\App\Model\User;

/**
 * Bootstrap class.
 */
class Bootstrap
{
    public $router;
    public $config;

    /**
     * Run instance.
     */
    public function run()
    {
        // Check important folders
        $this->checkFiles(array('Cache', 'Ajax', 'files'));

        // Load language
        $GLOBALS['_lang'] = new Lang();
        if (isset($_GET['lang'])) {
            $lang_slug = substr($_GET['lang'], 0, 3);
            $GLOBALS['_lang']->getFromSlug($lang_slug, false, $this->config->core['defaultLang']);
            $GLOBALS['_lang']->setCookie();
        } else {
            $GLOBALS['_lang']->getSiteLanguage($this->config->core['defaultLang'], $this->config->core['forcedLang']);
        }

        // Load user
        $GLOBALS['_user'] = new User();
        if (isset($_COOKIE[COOKIE_NAME.'_log'])) {
            $GLOBALS['_user']->getFromCookie($_COOKIE[COOKIE_NAME.'_log']);
        }

        session_start();
        if (isset($_SESSION['logged'])) {
            if ($_SESSION['logged'] == true && $_SESSION['uID'] != 0) {
                $GLOBALS['_user']->getFromId($_SESSION['uID']);
            }
        }

        // Routing
        if ($this->router) {
            $this->router->config = $this->config;

            if ((!$GLOBALS['_user']->logged || $GLOBALS['_user']->logged && !$GLOBALS['_user']->isAdmin())
                && ($this->config->core['maintenance'])
            ) {
                // Maintenance mode
                $this->router->dispatch('maintenance');
            } else {
                // Start routing
                $this->router->route();
            }
        }
    }

    /**
     * Check if exists some folders.
     */
    public function checkFiles($folders)
    {
        foreach ($folders as $folder) {
            if (!file_exists(GLOBAL_PATH.'/'.$folder.'/')) {
                mkdir(GLOBAL_PATH.'/'.$folder.'/', 0777, true);
            }
        }
    }
}
