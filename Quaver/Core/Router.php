<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Quaver\Core\Lang;
use Quaver\App\Model\User;

/**
 * Router
 * @package default
 */
class Router
{
    private $version = '0.8.3';

    // Language system
    public $language;

    // URL management
    public $url;
    public $queryString;
    
    // Template system
    public $twig = null;
    public $twigVars = array();

    /**
     * constructor
     */
    public function __construct()
    {
        global $_lang;

        $this->language = $_lang->id;

        // Theme system
        define('VIEW_PATH', GLOBAL_PATH . '/Quaver/App/Theme/' . THEME_QUAVER . '/View');
        define('RES_PATH', '/Quaver/App/Theme/' . THEME_QUAVER . '/Resources');
        define('CSS_PATH', RES_PATH . '/css');
        define('JS_PATH', RES_PATH . '/js');
        define('IMG_PATH', RES_PATH . '/img');
        define('FONT_PATH', RES_PATH . '/fonts');

        // Getting all directories in /template
        $path = VIEW_PATH;

        $templatesDir = array($path);
        $dirsToScan = array($path);

        $dirKey = 0;
        while (count($dirsToScan) > $dirKey) {
            $results = scandir($dirsToScan[$dirKey]);
            foreach ($results as $result) {
                if ($result === '.' || $result === '..') {
                    continue;
                }

                if (is_dir($dirsToScan[$dirKey] . '/' . $result)) {
                    $templatesDir[] = $dirsToScan[$dirKey] . '/' . $result;
                    $dirsToScan[] = $dirsToScan[$dirKey] . '/' . $result;
                }
            }
            $dirKey++;
        }

        // Get query string from URL to core var
        $this->getQueryString();

        // Create twig loader
        $loader = new \Twig_Loader_Filesystem($templatesDir);

        $twig_options = array();
        if (defined('TEMPLATE_CACHE') && TEMPLATE_CACHE) {
            $twig_options['cache'] = GLOBAL_PATH . "/Cache";
        }
        
        if (defined('CACHE_AUTO_RELOAD') && CACHE_AUTO_RELOAD) {
            $twig_options['auto_reload'] = true;
        }
        
        // Create twig object
        $this->twig = new \Twig_Environment($loader, $twig_options);

        // Clear Twig cache
        if (defined('TEMPLATE_CACHE') && TEMPLATE_CACHE) {
            if (isset($this->queryString['clearCache'])) {
                $this->twig->clearCacheFiles();
                $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                header("Location: $url");
                exit;
            }
        }

        // Restoring user_default session sample
        if (!empty($this->queryString['PHPSESSID'])) {
            $sessionHash = $this->queryString['PHPSESSID'];
            $_userFromSession = new User;
            $_userFromSession->setCookie($sessionHash);
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            header("Location: $url");
            exit;
        }


    }

    /**
     * route
     * @return type
     */
    public function route()
    {
        $route = $this->getCurrentRoute();
        $this->fixTrailingSlash($route);
        $view = $this->getView($route);
        if ($view != false) {
            $this->dispatch($view['controller']);
        }
    }

    /**
     * getCurrentURL
     * @param type $position 
     * @return type
     */
    public function getCurrentURL($position = 1)
    {
        $return = false;
        $length = count($this->url['uri']);

        if ($length == 0) {
            $return = $this->url['path'];
        } else {
            $return = $this->url['uri'][$position];
        }

        return $return;
    }

    /**
     * getCurrentRoute
     * @return type
     */
    public function getCurrentRoute()
    {
        $url = $_SERVER['REQUEST_URI'];

        if (strstr($url, "?") !== false) {
            $url = substr($url, 0, strpos($url, "?")); // Remove GET vars
        }

        return $url;
    }

    /**
     * fixTrailingSlash
     * @param type $_url 
     * @return type
     */
    public function fixTrailingSlash($_url)
    {
        if ($_url{strlen($_url) - 1} != '/' && strstr($_url, "image/") === false) {
            header("Location: " . $_url . "/");
            exit;
        }
    }

    /**
     * removeSlash
     * @param type $_url 
     * @return type
     */
    public function removeSlash($_url)
    {
        $url = str_replace('/', '', $_url);
        return $url;
    }

    /**
     * getView
     * @param type $url 
     * @return type
     */
    public function getView($_url)
    {
        $routes = null;
        $return = false;
        $view = false;

        try {
            $yaml = new Parser();
            $routes = $yaml->parse(file_get_contents(GLOBAL_PATH . '/Quaver/Routes.yml'));

        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        foreach ($routes as $item) {
            $regexp = "/^" . str_replace(array("/", "\\\\"), array("\/", "\\"), $item['url']) . "$/";
            preg_match($regexp, $_url, $match);

            if (@$match) {
                $this->url = array(
                    "uri" => array_splice($match, 1),
                    "path" => $match[0],
                );
                $view = $item;
                break;
            }
        }

        if ($view) {
            $return = $view;
        } else {
            $this->dispatch('e404');
        }

        return $return;
    }

    /**
     * dispatch
     * @param type $route 
     * @return type
     */
    public function dispatch($route)
    {

        global $_lang, $_user;
        
        if ($route) {
            $controllerPath = CONTROLLER_PATH . "/" . $route . ".php";

            $this->getGlobalTwigVars();

            // Load controller
            if (file_exists($controllerPath)) {
                require_once($controllerPath);
            } else {
                throw new \Quaver\Core\Exception("Error loading controller: $route");
            }
        }

    }

    /**
     * getQueryString
     * @return type
     */
    public function getQueryString()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $qs = parse_url($uri, PHP_URL_QUERY);
        if (!empty($qs)) {
            parse_str($qs, $this->queryString);
        }
    }

    /**
     * Set main variables
     */
    public function getGlobalTwigVars()
    {
        global $_lang, $_user;

        // Language
        $this->addTwigVars("language", $_lang);

        // Languages
        $languageVars = array();
        $ob_l = new Lang;
        foreach ($ob_l->getList() as $lang) {
            $item = array(
                "id" => $lang->id,
                "name" => utf8_encode($lang->name),
                "slug" => $lang->slug,
                "locale" => $lang->locale,
            );
            array_push($languageVars, $item);
        }
        $this->addTwigVars('languages', $languageVars);

        // User data
        $userVars = array(
            "admin" => $_user->isAdmin(),
            "logged" => $_user->logged,
            "sessionHash" => $_user->cookie,
        );
        $this->addTwigVars("user", $userVars);
        $this->addTwigVars("_user", $_user);

        // Login errors
        if (isset($this->queryString['login-error'])) {
            $this->addTwigVars('loginError', true);
        }

        if (isset($this->queryString['user-disabled'])) {
            $this->addTwigVars('userDisabled', true);
        }

        // Extra parametres
        $config = array(
            "theme" => THEME_QUAVER,
            "randomVar" => RANDOM_VAR,
            "css" => CSS_PATH,
            "js" => JS_PATH,
            "img" => IMG_PATH,
            "env" => DEV_MODE,
            "version" => $this->version,
            "url" => $this->url,
        );  

        if (strstr($this->url['path'], "/admin/")) {
            if (defined('DEV_MODE') && DEV_MODE == false) {
                $build = shell_exec("git log -1 --pretty=format:'%h - %s (%ci)' 
                    --abbrev-commit $(git merge-base local-master master)");
            } else {
                $build = shell_exec("git log -1 --pretty=format:'%h - %s (%ci)' 
                    --abbrev-commit $(git merge-base local-dev dev)");
            }

            $config['build'] = $build;
            
        }
        
        $this->addTwigVars('qv', $config);

    }

    /**
     * addTwigVars
     * @param type $_key 
     * @param type $_array 
     * @return type
     */
    public function addTwigVars($_key, $_array)
    {
        $this->twigVars[$_key] = $_array;
    }
}
