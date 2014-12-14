<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\Model\Lang;
use Quaver\Model\User;

class Core
{

    private $version = '0.6';

    // Language system
    public $language;

    /**
     * run
     * @param type $_mvc 
     * @return type
     */
    public function run($_mvc = true)
    {
        global $_lang, $_user;

        // Check important folders
        $this->checkFiles();

        // Load user
        $_user = new User;
        if (!empty($_COOKIE[COOKIE_NAME . "_log"])) {
            $_user->getFromCookie($_COOKIE[COOKIE_NAME . "_log"]);
        }

        session_start();
        if (isset($_SESSION['logged'])) {

            if ($_SESSION['logged'] == true && $_SESSION['uID'] != 0) {
                $_user->getFromId($_SESSION['uID']);
            }
            
        }

        // Load language
        $_lang = new Lang;
        if (!empty($_GET['lang'])) {
            $lang_slug = substr($_GET['lang'], 0, 3);
            $_lang->getFromSlug($lang_slug);
            $_lang->setCookie();
        } else {
            $_lang->getSiteLanguage();
        }
        $this->language = $_lang->id;

        // Maintenance mode
        if ((!$_user->logged || $_user->logged && !$_user->isAdmin())
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
