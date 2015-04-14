<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\Core\Lang;
use Quaver\Core\Router;
use Quaver\App\Model\User;

/**
 * Bootstrap class
 * @package Core
 */
class Bootstrap
{   

    public $router;

    /**
     * Run instance
     * @return type
     */
    public function run()
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
            if ($this->router) {
                $this->router->dispatch('maintenance');
            }
        } else {
            if ($this->router) {
                $this->router->route();   
            }
        }

        
        
    }

    /**
     * Check if exists some folders
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
