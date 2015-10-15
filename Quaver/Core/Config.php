<?php
/**
 * Quaver Framework
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

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Config class
 * @package Quaver
 */
class Config
{   
    public $db = NULL;
    public $cookies = NULL;
    public $params = NULL;
    public $plugins = NULL;
    private static $instance = NULL;
   
    private function __construct() { }
 
    public function __clone() { }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Read yml file to load config vars
     * @param string $file 
     * @return array
     */
    public function setEnvironment($file = '')
    {
        try {
            $yaml = new Parser();

            if (empty($file)) {
                $file = GLOBAL_PATH.'/../Config.yml';
            }
            $elements = $yaml->parse(file_get_contents($file));

            // Set DB/Cookie constants
            $this->db = $this->setDatabaseParams($elements['db']);
            unset($elements['db']);

            $this->cookies = $this->setCookieParams($elements['cookies']);
            unset($elements['cookies']);

            $configObj = (object) $elements;
            $configObj->core['modelPath'] = GLOBAL_PATH.$configObj->core['modelPath'];  
            $configObj->core['controllerPath'] = GLOBAL_PATH.$configObj->core['controllerPath'];

            $this->params = $configObj;
            return $configObj;
            
        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception('Unable to parse the YAML string: %s', $e->getMessage());
        }
    }

    /**
     * Set database constants
     * @param array $params
     */
    private function setDatabaseParams($params)
    {
        $params['hostname'] = $params['hostname']? $params['hostname']: 'localhost'; 
        $params['username'] = $params['username']? $params['username']: 'root';
        $params['password'] = $params['password']? $params['password']: 'root';
        $params['database'] = $params['database']? $params['database']: 'qv';
        $params['port'] = $params['port']? $params['port']: 3306;
        $params['devMode'] = $params['devMode']? $params['devMode']: true;
        $params['cypherKey'] = $params['cypherKey']? $params['cypherKey']: '';

        return $params;
    }

    /**
     * Set cookie constants
     * @param array $params
     */
    private function setCookieParams($params)
    {
        $params['cookieName'] = $params['cookieName']? $params['cookieName'] : 'quaver';
        $params['cookieDomain'] = $params['cookieDomain'] != 'server'? $params['cookieDomain']: $_SERVER['HTTP_HOST'];
        $params['cookiePath'] = $params['cookiePath']? $params['cookiePath'] : '/';

        return $params;
    }

    /**
     * setPluginsYML
     * @param array|object $modules 
     * @param bool $force 
     * @return object
     */
    public function setPluginsYML($modules, $force = false)
    {
        
        if (!file_exists(GLOBAL_PATH.'/../Plugins.yml') || $force) {
            
            try {

                $dumpModules = $modules;
                foreach ($dumpModules as $key => $module) {
                    $dumpModules[$key]['params'] = get_object_vars($module['params']);
                }
        
                $dumper = new Dumper();
                $yaml = $dumper->dump($dumpModules);
                file_put_contents(GLOBAL_PATH.'/../Plugins.yml', $yaml);

            } catch (DumpException $e) {
                throw new \Quaver\Core\Exception('Unable to dump the YAML string: %s', $e->getMessage());
            }
        }

        $this->getPluginsYML();
    }

    /**
     * getPluginsYML
     * @return self
     */
    public function getPluginsYML()
    {
        try {
            $yaml = new Parser();
            $elements = $yaml->parse(file_get_contents(GLOBAL_PATH.'/../Plugins.yml'));

            $this->plugins = (object) $elements;
        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception('Unable to parse the YAML string: %s', $e->getMessage());
        }
    }
}
