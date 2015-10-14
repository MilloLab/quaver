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
     * @param class $router
     *
     * @return mixed
     */
    public function __construct($router)
    {
        global $_lang, $_user;

        $this->router = $router;
        $config = Config::getInstance();

        // Theme system
        $theme = $config->params->app['theme'];
        $viewPath = GLOBAL_PATH.'/Quaver/App/Theme/'.$theme.'/View';
        $resPath = array(
            'view' => $viewPath,
            'res' => '/Quaver/App/Theme/'.$theme.'/Resources',
            'css' => '/Quaver/App/Theme/'.$theme.'/Resources/css',
            'js' => '/Quaver/App/Theme/'.$theme.'/Resources/js',
            'img' => '/Quaver/App/Theme/'.$theme.'/Resources/img',
            'font' => '/Quaver/App/Theme/'.$theme.'/Resources/fonts',
            'theme' => $theme,
            'randomVar' => $config->params->app['randomVar'],
        );
        $r = new Resources($resPath, $config->params->core['devMode']);
        $this->r = $r;

        // Getting all directories in /template
        $templatesDir = array($viewPath);

        // Get query string from URL to core var
        $this->router->getQueryString();

        // Create twig loader
        $loader = new \Twig_Loader_Filesystem($templatesDir);

        // Add paths of modules
        if ($config->plugins) {
            foreach ($config->plugins as $module) {
                if ($module['params']['useViews'] == true && isset($module['params']['theme']) && !empty($module['params']['theme'])) {
                    $loader->addPath($module['realPath'].$module['namespacePath'].'/Theme/'.$module['params']['theme'].'/View');
                }
            }
        }

        $twig_options = array();
        if ($config->params->core['templateCache']) {
            $twig_options['cache'] = GLOBAL_PATH.'/Cache';
        }

        if ($config->params->core['cacheAutoReload']) {
            $twig_options['auto_reload'] = true;
        }

        // Create twig object
        $this->twig = new \Twig_Environment($loader, $twig_options);

        // Create a custom filter to translate strings
        $filter = new \Twig_SimpleFilter('t', function ($string) {
            return $GLOBALS['_lang']->typeFormat($string, 'd');
        });
        $this->twig->addFilter($filter);

        if ($config->params->core['devMode'] && $config->params->core['benchMark']) {
            $this->twigProfiler = new \Twig_Profiler_Profile();
            $this->twig->addExtension(new \Twig_Extension_Profiler($this->twigProfiler));
        }

        // Clear Twig cache
        if ($config->params->core['templateCache']) {
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
     * @param string $path
     * @param string $extension
     *
     * @return mixed
     */
    public function setView($path, $extension = 'twig')
    {
        $this->template = $this->twig->loadTemplate($path.'.'.$extension);
    }

    /**
     * Render views.
     *
     * @return mixed
     */
    public function render()
    {
        $config = Config::getInstance();
        $showTemplate = $this->template->render($this->twigVars);

        if ($config->params->core['devMode']
            && $config->params->core['benchMark'] && $this->router->dev) {

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
     */
    protected function getGlobalTwigVars()
    {
        $config = Config::getInstance();

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
        $configVars = array(
            'extra' => array(
                'brandName' => $config->params->app['brandName'],
            ),
            'randomVar' => $config->params->app['randomVar'],
            'r' => $this->r,
            'env' => $config->params->core['devMode'] ? 'development' : 'production',
            'dev' => $this->router->dev,
            'version' => $this->router->version,
            'url' => $this->router->url,
            'language' => $GLOBALS['_lang'],
            'languages' => $languageVars,
            'user' => $GLOBALS['_user'],
            'modules' => $this->router->modules,
            'routes' => $this->router->routes,
            'config' => get_object_vars($config->params),
            'plugins' => get_object_vars($config->plugins),
        );

        if (strstr($this->router->url['path'], '/admin/')) {
            $build = shell_exec("git log -1 --pretty=format:'%H (%aD)'");
            $configVars['build'] = $build;
        }

        $this->configVars = $configVars;
        $this->addTwigVars('qv', $this->configVars);
    }

    /**
     * Add vars to twig.
     *
     * @param string $_key
     * @param array $_array
     *
     */
    public function addTwigVars($_key, $_array)
    {
        $this->twigVars[$_key] = $_array;
    }

    /**
     * Extend qv object for twig.
     *
     * @param string $_key
     * @param array $_array
     *
     */
    public function addQuaverTwigVars($_key, $_array)
    {
        $this->configVars[$_key] = $_array;
        $this->addTwigVars('qv', $this->configVars);
    }
}
