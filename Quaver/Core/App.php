<?php
/**
 * Quaver Framework.
 *
 * @author      Alberto González <quaver@millolab.com>
 * @copyright   2014 Alberto González
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Quaver\Core;

use Quaver\App\Model\User;

/**
 * App.
 *
 * @author   Alberto González
 */
class App
{
    /**
     * @const string
     */
    const VERSION = '0.11.2';

    /**
     * @var \Quaver\Core\Router
     */
    public $router;

    /**
     * @var \Quaver\Core\Controller
     */
    public $modules;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set router
        $this->router = new \Quaver\Core\Router();
        $this->router->version = self::VERSION;

        // Set config
        $config = \Quaver\Core\Config::getInstance();
        $config->setEnvironment();
        if ($config->params->core['devMode'] && $config->params->core['benchMark']) {
            $this->router->startBenchProcess(); //false argument to stop
        }
    }

    /**
     * Add new paths (YAML).
     *
     * @param string $container
     * @param string $path
     * @param bool   $_moduleRoute
     *
     * @return array
     */
    public function addPath($container, $path, $_moduleRoute = false)
    {
        $this->router->addPath($container, $path, $_moduleRoute);
    }

    /**
     * Add modules to Quaver.
     *
     * @param string $moduleName
     * @param string $packageName
     * @param string $modulePath
     * @param string $moduleRoute
     *
     * @return array
     */
    public function addModule($moduleName, $packageName, $modulePath = '', $moduleRoute = '/')
    {
        $this->router->addModule($moduleName, $packageName, $modulePath, $moduleRoute);
    }

    /**
     * Remove module.
     *
     * @param string $moduleName
     */
    public function removeModule($moduleName)
    {
        $this->router->removeModule($moduleName);
    }

    /**
     * @return object
     */
    public function modules()
    {
        return $this->router->modules;
    }

    /**
     * Run instance.
     */
    public function run()
    {
        $config = Config::getInstance();

        // Start db
        $db = DB::getInstance();
        $db->setConnection();

        // Load default routes
        if (!$this->router->routes) {
            $this->addPath('/', GLOBAL_PATH.'/../Routes.yml');
        }

        // Manage folders and plugins
        if (!defined('AJAX_METHOD')) {
            // Create if not exist
            $this->checkFiles($config->params->folders);

            // Start plugins
            $config->setPluginsYML($this->router->modules);

            // Load routes of module
            foreach ($config->plugins as $key => $plugin) {
                if ($plugin['enabled']) {
                    if ($plugin['params']['useRoutes']) {
                        $this->router->addPath('/', VENDOR_PATH.'/'.$plugin['packageName'].'/'.$plugin['namespacePath'].'/'.'Routes.yml', true);
                    }
                }
            }
        }

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
        if (!defined('AJAX_METHOD')) {
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
            if (!file_exists(GLOBAL_PATH.'/../'.$folder.'/')) {
                mkdir(GLOBAL_PATH.'/../'.$folder.'/', 0777, true);
            }
        }
    }
}
