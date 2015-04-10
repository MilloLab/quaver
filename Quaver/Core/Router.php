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

class Router
{
    public $version = '0.9';
    private $routes;

    // Language system
    public $language;

    // URL management
    public $url;
    public $queryString;

    /**
     * constructor
     */
    public function __construct()
    {

        $this->routes = array();

        if (isset($GLOBALS['_lang'])) {
            $this->language = $GLOBALS['_lang']->id;
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
        $controller = $this->getController($route);

        if ($controller != false) {
            $this->dispatch($controller);
        }
    }

    /**
     * addPath
     * @param type $container 
     * @param type $path 
     * @return type
     */
    public function addPath($container, $path)
    {
        try {

            $yaml = new Parser();
            $elements = $yaml->parse(file_get_contents($path));

            isset($this->routes[$container]) ? $this->routes[$container] += $elements: $this->routes[$container] = $elements;

        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception("Unable to parse the YAML string: %s", $e->getMessage());
        }           
    }
    
    /**
     * getUrlPart
     * @param type $position 
     * @return type
     */
    public function getUrlPart($position)
    {
        if (isset($this->url['uri'][$position])) {
            return $this->url['uri'][$position];
        }
        return null;
    }

    /**
     * getCurrentURL
     * @param type $position 
     * @return type
     */
    public function getCurrentURL($position = 0)
    {
        $return = false;
        $length = count($this->url['uri']);

        if ($position == 0 && $length > 0) {
            $position = $length - 1;

            if (is_numeric($this->getUrlPart($position))) {
                $position -= 1;                
            }
        }

        if ($length == 0) {
            $return = $this->url['path'];
        } else {
            $return = $this->getUrlPart($position);
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
     * getController
     * @param type $url 
     * @return type
     */
    public function getController($_url)
    {

        $return = false;
        $controller = false;

        foreach ($this->routes as $indexPath => $container) {
            
            $regexp = "/^" . str_replace(array("/", "\\\\"), array("\/", "\\"), $indexPath) . "/";
            preg_match($regexp, $_url, $match);

            if ($match) {

                foreach ($container as $item) {

                    $regexp = "/^" . str_replace(array("/", "\\\\"), array("\/", "\\"), $item['url']) . "$/";
                    preg_match($regexp, $_url, $match);

                    if ($match) {
                        $this->url = array(
                            "uri" => array_splice($match, 1),
                            "path" => $match[0],
                            "host" => $_SERVER['HTTP_HOST'],
                            "protocol" => empty($_SERVER['HTTPS'])? 'http://' : 'https://'
                        );
                        $controller = $item;
                        break;
                    }
                }
            }

            
        }

        if (isset($controller['controller'])) {
            $return = $controller;
        } else {
            $this->dispatch('e404');
        }

        return $return;
    }

    /**
     * dispatch
     * @param type $controller 
     * @return type
     */
    public function dispatch($controller)
    {

        global $_lang, $_user;

        if ($controller == 'e404') {
            $controller = $this->routes['/']['404'];
        }
        
        if ($controller) {

            if (isset($controller['path'])) {
                $controllerPath = $controller['path'];
                $pathNamespace = $controllerPath . '\\';
            } else {
                $controllerPath = '';
                $pathNamespace = '';
            }


            if (isset($controller['view'])) {
                $controllerView = $controller['view'];
            }
            
            $controllerURL = $controller['url'];
            $controllerName = $controller['controller'];
            $controllerNamespace = '\\Quaver\\App\\Controller\\' . $pathNamespace . $controllerName;
            $actionName = $controller['action'] . 'Action';

            $realPath = !empty($controllerPath) ? CONTROLLER_PATH . '/' . $controllerPath . '/' . $controllerName . '.php' : CONTROLLER_PATH . '/' . $controllerName . '.php';

            // Load controller
            if (file_exists($realPath)) {
                
                $controller = new $controllerNamespace($this);

                if (isset($controllerView) && $controllerView != 'none') {
                    
                    $controller->setView($controllerView);
                }

                $controller->$actionName();
                
            } else {
                throw new \Quaver\Core\Exception("Error loading controller: " . $controller['controller']);
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
}
