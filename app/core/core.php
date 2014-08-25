<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * core class
 */
class core {

    // DB object
    public $db; 

	// URL management
    public $url_var;
    public $queryString;

	// Language system
    public $language;
    
    // Template system
    public $twig = null;
    public $twigVars = array();

    // Development
    public $debug = false;


    /**
     * constructor
     */
    public function __construct() {

        // Create new DB object
        $this->db = new DB;

        // Twig Template System Loader
        require_once(LIB_PATH . '/Twig/Autoloader.php');
        Twig_Autoloader::register();

        // Getting all directories in /template
        $path = VIEW_PATH;

        $templatesDir = array($path);
        $dirsToScan = array($path);

        $dirKey = 0;
        while (count($dirsToScan) > $dirKey) {
            $results = scandir($dirsToScan[$dirKey]);
            foreach ($results as $result) {
                if ($result === '.' or $result === '..') continue;

                if (is_dir($dirsToScan[$dirKey] . '/' . $result)) {
                    $templatesDir[] = $dirsToScan[$dirKey] . '/' . $result;
                    $dirsToScan[] = $dirsToScan[$dirKey] . '/' . $result;
                }
            }
            $dirKey++;
        }

		//get query string from URL to core var
        $this->getQueryString();
        $loader = new Twig_Loader_Filesystem($templatesDir);

        $twig_options = array();
        if (defined(TEMPLATE_CACHE) && TEMPLATE_CACHE) $twig_options['cache'] = "./cache";
        if (defined(CACHE_AUTO_RELOAD) && CACHE_AUTO_RELOAD) $twig_options['auto_reload'] = true;
        
        $this->twig = new Twig_Environment($loader, $twig_options);

        // Clear cache
        if (isset($this->queryString['clearCache'])) {
            $this->twig->clearCacheFiles();
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            header("Location: $url");
            exit;
        }

        # Restoring user session
        


    }

    /**
     * @param bool $_mvc
     */
    public function start($_mvc = true) {
    	
        global $_lang, $_user;
        
        # Check user login
        

        // Load language
        $_lang = new lang;
        if (!empty($_GET['lang'])) {
            $lang_slug = substr($_GET['lang'], 0, 3);
            $_lang->getFromSlug($lang_slug);
            $_lang->setCookie();
        } else {
            $_lang->getSiteLanguage();
        }
        $this->language = $_lang->id;

        // Assoc URL to MVC
        if ($_mvc) $this->loadMVC();
    }


    /**
     * Load architecture
     */
    public function loadMVC()
    {
        $url = $this->getUrl();
        $this->fixTrailingSlash($url);
        $mvc = $this->getVT($url);
        if ($mvc != false) {

            $this->setController($mvc['controller']);
            
        } else {

            $msg = "Controller instance not found.";
            error_log($msg);
            echo "<h1>{$msg}</h1>";
            die;

        }
    }

    /**
     * @return string
     */
    public function getUrl() {
        $url = $_SERVER['REQUEST_URI'];
        if (strstr($url, "?") !== false)
            $url = substr($url, 0, strpos($url, "?")); // Remove GET vars
        return $url;
    }

    /**
     * @param $_url
     */
    public function fixTrailingSlash($_url) {

        if ($_url{strlen($_url) - 1} != '/' && strstr($_url, "image/") === false) {
            header("Location: " . $_url . "/");
            exit;
        }
    }

    /**
     * @param $_url
     * @return mixed
     */
    public function getVT($_url) {
        $routes = null;

        try {
            $yaml = new Parser();
            $routes = $yaml->parse(file_get_contents('./app/routes.yml'));

        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        foreach ($routes as $item) {
            $regexp = "/^" . str_replace(array("/", "\\\\"), array("\/", "\\"), $item['url']) . "$/";
            preg_match($regexp, $_url, $match);

            if (@$match) {
                $this->url_var = $match;
                $mvc = $item;
                break;
            }
        }

        if (@$mvc) {
            $return = $mvc;
        } else {
            $this->setController('404');
            //die('error 404');
        }
        return $return;
    }


    /**
     * @param $_controllerName
     */
    public function setController($_controllerName) {

        global $_user, $_lang;
        
        $controllerPath = CONTROLLER_PATH . "/" . $_controllerName . ".php";

        $this->getGlobalTwigVars();

        // Load controller
        if (file_exists($controllerPath)) {
            require_once($controllerPath);
        } else {
            if (!empty($_controllerName)){
                $msg = "Error loading controller: $_controllerName";
                error_log($msg);
                echo "<h1>{$msg}</h1>";
                die;
            }

        }
    }


    /**
     * URL parser
     */
    public function getQueryString() {
        $uri = $_SERVER['REQUEST_URI'];
        $qs = parse_url($uri, PHP_URL_QUERY);
        if (!empty($qs)) {
            parse_str($qs, $this->queryString);
        }            
    }


    /**
     * Set main variables
     */
    public function getGlobalTwigVars() {
        global $_user, $_lang;

        // Language
        $this->addTwigVars("language", $_lang);

        // Environment
        $this->addTwigVars("_env", DEV_MODE);

        // Languages
        $languageVars = array();
        $ob_l = new lang;
        foreach ($ob_l->getList() as $lang) {
            $item = array(
                "id" => $lang->id,
                "domain" => HTTP_MODE . "www." . DOMAIN_NAME . $lang->tld,
                "name" => utf8_encode($lang->name),
                "slug" => $lang->slug,
                "locale" => $lang->locale,
                "url" => "language/" . $lang->slug . "/",
                "class" => ($_lang->id == $lang->id) ? 'selected' : ''
            );
            array_push($languageVars, $item);
        }
        $this->addTwigVars('languages', $languageVars);

        $this->addTwigVars('actual_url', strip_tags($this->url_var[0]));

        // Config
        $config = array(
            "baseHref" => HTTP_MODE . DOMAIN_NAME,
            "thisHref" => HTTP_MODE . DOMAIN_NAME . $this->getUrl(),
            "randomVar" => RANDOM_VAR
        );

        $this->addTwigVars('config', $config);

    }

    /**
     * @param $_key
     * @param $_array
     */
    public function addTwigVars($_key, $_array) {
        $this->twigVars[$_key] = $_array;
    }


}
?>