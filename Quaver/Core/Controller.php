<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

/**
 * Controller base class.
 */
abstract class Controller
{
    public $router;

    // Template system
    public $template;
    public $twig = null;
    public $twigVars = array();
    public $twigProfiler;
    public $configVars = array();

    /**
     * Router constructor.
     *
     * @param type $router
     *
     * @return type
     */
    public function __construct($router)
    {
        global $_lang, $_user;

        $this->router = $router;

        // Theme system
        define('VIEW_PATH', GLOBAL_PATH.'/Quaver/App/Theme/'.THEME_QUAVER.'/View');
        define('RES_PATH', '/Quaver/App/Theme/'.THEME_QUAVER.'/Resources');
        define('CSS_PATH', RES_PATH.'/css');
        define('JS_PATH', RES_PATH.'/js');
        define('IMG_PATH', RES_PATH.'/img');
        define('FONT_PATH', RES_PATH.'/fonts');

        // Getting all directories in /template
        $templatesDir = array(VIEW_PATH);

        // Get query string from URL to core var
        $this->router->getQueryString();

        // Create twig loader
        $loader = new \Twig_Loader_Filesystem($templatesDir);

        // Add paths of modules
        if ($this->router->modules) {
            foreach ($this->router->modules as $module) {
                if ($module['params']->useViews === true && isset($module['params']->theme) && !empty($module['params']->theme)) {
                    $loader->addPath($module['realPath'].$module['namespacePath'].'/Theme/'.$module['params']->theme.'/View');
                }
            }
        }

        $twig_options = array();
        if (defined('TEMPLATE_CACHE') && TEMPLATE_CACHE) {
            $twig_options['cache'] = GLOBAL_PATH.'/Cache';
        }

        if (defined('CACHE_AUTO_RELOAD') && CACHE_AUTO_RELOAD) {
            $twig_options['auto_reload'] = true;
        }

        // Create twig object
        $this->twig = new \Twig_Environment($loader, $twig_options);

        // Create a custom filter to translate strings
        $filter = new \Twig_SimpleFilter('t', function ($string) {
            return $GLOBALS['_lang']->typeFormat($string, 'd');
        });
        $this->twig->addFilter($filter);

        if (defined('DEV_MODE') && DEV_MODE) {
            $this->twigProfiler = new \Twig_Profiler_Profile();
            $this->twig->addExtension(new \Twig_Extension_Profiler($this->twigProfiler));
        }

        // Clear Twig cache
        if (defined('TEMPLATE_CACHE') && TEMPLATE_CACHE) {
            if (isset($this->router->queryString['clearCache'])) {
                $this->twig->clearCacheFiles();
                $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                header("Location: $url");
                exit;
            }
        }

        $this->getGlobalTwigVars();
    }

    /**
     * Asociate views to render.
     *
     * @param type $path
     * @param type $extension
     *
     * @return type
     */
    public function setView($path, $extension = 'twig')
    {
        $this->template = $this->twig->loadTemplate($path.'.'.$extension);
    }

    /**
     * Render views.
     *
     * @return type
     */
    public function render()
    {
        $showTemplate = $this->template->render($this->twigVars);

        if (defined('DEV_MODE') && DEV_MODE && $this->router->dev) {
            $this->router->finishBenchProcess();

            $dumper = new \Twig_Profiler_Dumper_Text();
            $this->router->dev['twigProfiler'] = $dumper->dump($this->twigProfiler);

            $this->addQuaverTwigVars('dev', $this->router->dev);
            $this->addTwigVars('qv', $this->configVars);

            $showTemplate = $this->template->render($this->twigVars);
        }

        echo $showTemplate;
    }

    /**
     * Set main twig variables.
     *
     * @return type
     */
    protected function getGlobalTwigVars()
    {
        // Language
        $this->addTwigVars('language', $GLOBALS['_lang']); // legacy support

        // Languages
        $languageVars = array();
        $ob_l = new Lang();
        $langList = $ob_l->getList();

        foreach ($langList as $lang) {
            $item = array(
                'id' => $lang->id,
                'name' => utf8_encode($lang->name),
                'large' => utf8_encode($lang->large),
                'slug' => $lang->slug,
                'locale' => $lang->locale,
            );
            array_push($languageVars, $item);
        }
        $this->addTwigVars('languages', $languageVars); // legacy support

        // Load user data
        $this->addTwigVars('_user', $GLOBALS['_user']); // legacy support

        // Login errors
        if (isset($this->router->queryString['login-error'])) {
            $this->addTwigVars('loginError', true);
        }

        if (isset($this->router->queryString['user-disabled'])) {
            $this->addTwigVars('userDisabled', true);
        }

        // Extra parametres
        $config = array(
            'theme' => THEME_QUAVER,
            'brandName' => BRAND_NAME,
            'randomVar' => RANDOM_VAR,
            'css' => CSS_PATH,
            'js' => JS_PATH,
            'img' => IMG_PATH,
            'env' => DEV_MODE ? 'development' : 'production',
            'dev' => $this->router->dev,
            'version' => $this->router->version,
            'url' => $this->router->url,
            'language' => $GLOBALS['_lang'],
            'languages' => $languageVars,
            'user' => $GLOBALS['_user'],
            'modules' => $this->router->modules,
            'routes' => $this->router->routes,

        );

        if (strstr($this->router->url['path'], '/admin/')) {
            $build = shell_exec("git log -1 --pretty=format:'%H (%aD)'");
            $config['build'] = $build;
        }

        $this->configVars = $config;
        $this->addTwigVars('qv', $this->configVars);
    }

    /**
     * Add vars to twig.
     *
     * @param type $_key
     * @param type $_array
     *
     * @return type
     */
    public function addTwigVars($_key, $_array)
    {
        $this->twigVars[$_key] = $_array;
    }

    /**
     * Extend qv object for twig.
     *
     * @param type $_key
     * @param type $_array
     *
     * @return type
     */
    public function addQuaverTwigVars($_key, $_array)
    {
        $this->configVars[$_key] = $_array;
        $this->addTwigVars('qv', $this->configVars);
    }
}
