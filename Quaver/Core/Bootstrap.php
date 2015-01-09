<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\Core\Lang;
use Quaver\App\Model\User;

class Bootstrap
{
    /**
     * run
     * @param type $_mvc 
     * @return type
     */
    public function run($_mvc = true)
    {

        // Check important folders
        $this->checkFiles();

        // Load user
        $GLOBALS['_user'] = new User;
        if (isset($_COOKIE[COOKIE_NAME . "_log"])) {
            $GLOBALS['_user']->getFromCookie($_COOKIE[COOKIE_NAME . "_log"]);
        }

        session_start();
        if (isset($_SESSION['logged'])) {

            if ($_SESSION['logged'] == true && $_SESSION['uID'] != 0) {
                $GLOBALS['_user']->getFromId($_SESSION['uID']);
            }
            
        }

        // Load language
        $GLOBALS['_lang'] = new Lang;
        if (isset($_GET['lang'])) {
            $lang_slug = substr($_GET['lang'], 0, 3);
            $GLOBALS['_lang']->getFromSlug($lang_slug);
            $GLOBALS['_lang']->setCookie();
        } else {
            $GLOBALS['_lang']->getSiteLanguage();
        }

        // Maintenance mode
        if ((!$GLOBALS['_user']->logged || $GLOBALS['_user']->logged && !$GLOBALS['_user']->isAdmin())
            && (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE)
        ) {
            header("Location: /maintenance/");
            exit;
        }

        // Assoc URL to MVC
        if ($_mvc) {
            $router = new Router();
            $router->route();
        }
    }

    /**
     * checkFiles
     * @return type
     */
    public function checkFiles()
    {

        if (!file_exists(GLOBAL_PATH . '/Cache/')) {
            mkdir(GLOBAL_PATH . '/Cache/', 0777, true);
        }

        if (!file_exists(GLOBAL_PATH . '/files/')) {
            mkdir(GLOBAL_PATH . '/files/', 0777, true);
        }

        if (!file_exists(GLOBAL_PATH . '/Ajax/')) {
            mkdir(GLOBAL_PATH . '/Ajax/', 0777, true);
        }

    }
}
