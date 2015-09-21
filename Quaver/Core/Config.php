<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Config class.
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
                $file = GLOBAL_PATH.'/Quaver/Config.yml';
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
        
        if (!file_exists(GLOBAL_PATH.'/Quaver/Plugins.yml') || $force) {
            
            try {

                $dumpModules = $modules;
                foreach ($dumpModules as $key => $module) {
                    $dumpModules[$key]['params'] = get_object_vars($module['params']);
                }
        
                $dumper = new Dumper();
                $yaml = $dumper->dump($dumpModules);
                file_put_contents(GLOBAL_PATH.'/Quaver/plugins.yml', $yaml);

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
            $elements = $yaml->parse(file_get_contents(GLOBAL_PATH.'/Quaver/Plugins.yml'));

            $this->plugins = (object) $elements;
        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception('Unable to parse the YAML string: %s', $e->getMessage());
        }
    }
}
