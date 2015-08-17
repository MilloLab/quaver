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

    /**
     * Run instance.
     */
    public function run()
    {
        $config = Config::getInstance();
        
        // Start db
        $db = DB::getInstance();
        $db->setConnection();

        // Create if not exist
        $this->checkFiles($config->params->folders);
        
        // Load language
        $GLOBALS['_lang'] = new Lang();
        if (isset($_GET['lang'])) {
            $lang_slug = substr($_GET['lang'], 0, 3);
            $GLOBALS['_lang']->getFromSlug($lang_slug, false, $config->params->core['defaultLang']);
            $GLOBALS['_lang']->setCookie();
        } else {
            $GLOBALS['_lang']->getSiteLanguage($config->params->core['defaultLang'], $config->params->core['forcedLang']);
        }

        // Load user
        $GLOBALS['_user'] = new User();
        if (isset($_COOKIE[$config->cookies['cookieName'].'_log'])) {
            $GLOBALS['_user']->getFromCookie($_COOKIE[$config->cookies['cookieName'].'_log']);
        }

        // Session
        session_start();
        if (isset($_SESSION['logged'])) {
            if ($_SESSION['logged'] == true && $_SESSION['uID'] != 0) {
                $GLOBALS['_user']->getFromId($_SESSION['uID']);
            }
        }

        // Routing
        if ($this->router) {
            if ((!$GLOBALS['_user']->logged || $GLOBALS['_user']->logged && !$GLOBALS['_user']->isAdmin())
                && ($config->params->core['maintenance'])
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
    private function checkFiles($folders)
    {
        foreach ($folders as $folder) {
            if (!file_exists(GLOBAL_PATH.'/'.$folder.'/')) {
                mkdir(GLOBAL_PATH.'/'.$folder.'/', 0777, true);
            }
        }
    }
}
