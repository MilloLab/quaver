<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Quaver\App\Model\User;

/**
 * Router class.
 */
class Router
{
    public $version = '0.9.8';
    public $routes;
    public $modules;

    // Language system
    public $language;

    // URL management
    public $url;
    public $queryString;

    // DevMode
    public $dev = array();

    /**
     * Router constructor.
     *
     * @return type
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
            $_userFromSession = new User();
            $_userFromSession->setCookie($sessionHash);
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            header("Location: $url");
            exit;
        }
    }

    /**
     * Routing flow.
     *
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
     * Add new paths (YAML).
     *
     * @param type $container
     * @param type $path
     * @param type $_moduleRoute
     *
     * @return type
     */
    public function addPath($container, $path, $_moduleRoute = false)
    {
        try {
            $yaml = new Parser();
            $elements = $yaml->parse(file_get_contents($path));

            if ($_moduleRoute) {
                $elements[key($elements)]['moduleRoute'] = $_moduleRoute;
            }

            // Asign each routes
            isset($this->routes[$container]) ? $this->routes[$container] += $elements : $this->routes[$container] = $elements;
        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception('Unable to parse the YAML string: %s', $e->getMessage());
        }
    }

    /**
     * Add modules to Quaver.
     *
     * @param type $moduleName
     * @param type $packageName
     * @param type $modulePath
     * @param type $moduleRoute
     *
     * @return type
     */
    public function addModule($moduleName, $packageName, $modulePath = '', $moduleRoute = '/')
    {
        try {
            $namespace = '\\Quaver\\'.$moduleName.'\\App';
            $namespacePath = '/Quaver/'.$moduleName.'/App';

            // Load config class of module
            $class = $namespace.'\\Config';
            $newModule = new $class();
            $newModule->getParams();

            // Load module configuration
            isset($this->modules[$moduleName]) ? $this->modules[$moduleName]['params'] += $newModule : $this->modules[$moduleName]['params'] = $newModule;

            // Set namespace and paths vars
            $this->modules[$moduleName]['namespace'] = $namespace;
            $this->modules[$moduleName]['namespacePath'] = $namespacePath;
            $this->modules[$moduleName]['packageName'] = $packageName;
            $this->modules[$moduleName]['realPath'] = $modulePath ? $modulePath.'/'.$packageName : VENDOR_PATH.'/'.$packageName;

            // Load routes of module

            if ($newModule->useRoutes) {
                !empty($modulePath) ? $this->addPath($moduleRoute, $modulePath.'/'.$packageName.'/'.$namespacePath.'/'.'Routes.yml', true) : $this->addPath($moduleRoute, VENDOR_PATH.'/'.$packageName.'/'.$namespacePath.'/'.'Routes.yml', true);
            }
        } catch (\Quaver\Core\Exception $e) {
            throw new \Quaver\Core\Exception("Unable to load module: $moduleName", $e->getMessage());
        }
    }

    /**
     * Get actual URI.
     *
     * @param type $position
     *
     * @return type
     */
    public function getUrlPart($position)
    {
        if (isset($this->url['uri'][$position])) {
            return $this->url['uri'][$position];
        }

        return;
    }

    /**
     * Get current URL/URI.
     *
     * @param type $position
     *
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
     * Get current action to route.
     *
     * @return type
     */
    public function getCurrentRoute()
    {
        $url = $_SERVER['REQUEST_URI'];

        if (strstr($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?')); // Remove GET vars
        }

        return $url;
    }

    /**
     * Fix trailing slash.
     *
     * @param type $_url
     *
     * @return type
     */
    public function fixTrailingSlash($_url)
    {
        if ($_url{strlen($_url) - 1} != '/' && strstr($_url, 'image/') === false) {
            header('Location: '.$_url.'/');
            exit;
        }
    }

    /**
     * Remove slash.
     *
     * @param type $_url
     *
     * @return type
     */
    public function removeSlash($_url)
    {
        $url = str_replace('/', '', $_url);

        return $url;
    }

    /**
     * Get asociate controller.
     *
     * @param type $url
     *
     * @return type
     */
    protected function getController($_url)
    {
        $return = false;
        $controller = false;

        foreach ($this->routes as $indexPath => $container) {
            $regexp = '/^'.str_replace(array('/', '\\\\'), array("\/", '\\'), $indexPath).'/';
            preg_match($regexp, $_url, $match);

            if ($match) {
                foreach ($container as $item) {
                    $regexp = '/^'.str_replace(array('/', '\\\\'), array("\/", '\\'), $item['url']).'$/';
                    preg_match($regexp, $_url, $match);

                    if ($match) {
                        $this->url = array(
                            'uri' => array_splice($match, 1),
                            'path' => $match[0],
                            'host' => $_SERVER['HTTP_HOST'],
                            'protocol' => empty($_SERVER['HTTPS']) ? 'http://' : 'https://',
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
     * Dispatch action.
     *
     * @param type $controller
     *
     * @return type
     */
    public function dispatch($controller)
    {
        global $_lang, $_user;

        try {

            // Special controllers
            if ($controller == 'e404') {
                $controller = $this->routes['/']['404'];
            }
            if ($controller == 'maintenance') {
                $controller = $this->routes['/']['maintenance'];
            }

            // Set controller data
            $controllerData = $this->setControllerData($controller);

            // Dispatch controller or module controller
            if ($controllerData['moduleRoute']) {
                foreach ($this->modules as $module) {
                    $moduleNamespace = $module['namespace'];
                    $realModulePath = !empty($controllerData['controllerPath']) ? $module['realPath'].$module['namespacePath'].'/Controller/'.$controllerData['controllerPath'].'/'.$controllerData['controllerName'].'.php' : $module['realPath'].$module['namespacePath'].'/Controller/'.$controllerData['controllerName'].'.php';

                    // Try to load module controller
                    if (file_exists($realModulePath)) {
                        $controllerData['controllerNamespace'] = $moduleNamespace.$controllerData['pathNamespace'].'\\Controller\\'.$controllerData['controllerName'];
                        $controllerLoader = new $controllerData['controllerNamespace']($this);

                        if (isset($controllerData['controllerView']) && $controllerData['controllerView'] != 'none' && $module['params']->useViews === true) {
                            $controllerLoader->setView($controllerData['controllerView']);
                        }

                        $controllerLoader->$controllerData['actionName']();
                    }
                }
            } else {
                // Try to load controller
                if (file_exists($controllerData['realPath'])) {
                    $controllerLoader = new $controllerData['controllerNamespace']($this);

                    if (isset($controllerData['controllerView']) && $controllerData['controllerView'] != 'none') {
                        $controllerLoader->setView($controllerData['controllerView']);
                    }

                    $controllerLoader->$controllerData['actionName']();
                }
            }
        } catch (\Quaver\Core\Exception $e) {
            throw new \Quaver\Core\Exception('Unable to load controller: '.$controller['controller'], $e->getMessage());
        }
    }

    /**
     * Set all info to manage controller dispatch.
     *
     * @param type $controller
     *
     * @return type
     */
    protected function setControllerData($controller)
    {
        $controllerData = array();

        // Set module route
        isset($controller['moduleRoute']) ? $controllerData['moduleRoute'] = true : $controllerData['moduleRoute'] = false;

        if (isset($controller['path'])) {
            $controllerData['controllerPath'] = $controller['path'];
            $pathNamespace = $controllerData['controllerPath'].'\\';
        } else {
            $controllerData['controllerPath'] = '';
            $pathNamespace = '';
        }

        if (isset($controller['view'])) {
            $controllerData['controllerView'] = $controller['view'];
        }

        $defaultNamespace = '\\Quaver\\App\\Controller\\';

        $controllerData['controllerURL'] = $controller['url'];
        $controllerData['controllerName'] = $controller['controller'];
        $controllerData['controllerNamespace'] = $defaultNamespace.$pathNamespace.$controllerData['controllerName'];
        $controllerData['actionName'] = isset($controller['action']) ? $controller['action'].'Action' : 'indexAction';
        $controllerData['realPath'] = !empty($controllerData['controllerPath']) ? CONTROLLER_PATH.'/'.$controllerData['controllerPath'].'/'.$controllerData['controllerName'].'.php' : CONTROLLER_PATH.'/'.$controllerData['controllerName'].'.php';

        // USE ONLY TO MODULES
        $controllerData['pathNamespace'] = $pathNamespace;

        return $controllerData;
    }

    /**
     * Get query string from URI.
     *
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
     * Start benchmarking.
     *
     * @param type $set
     *
     * @return type
     */
    public function startBenchProcess($set = true)
    {
        if ($set) {
            $start_time = microtime(true);
            $this->dev['start_time'] = $start_time;
        }
    }

    /**
     * Finish benchmarking.
     *
     * @return type
     */
    public function finishBenchProcess()
    {
        $end_time = microtime(true);

        $this->dev['end_time'] = $end_time;
        $this->dev['final_time'] = ($end_time - $this->dev['start_time']).' ms';
        $this->dev['memory_usage'] = 'memory: '.(memory_get_peak_usage() / 1024 / 1024).' MB';
    }
}
