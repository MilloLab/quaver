<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Config class.
 */
class Config
{   
    /**
     * Read yml file to load config vars
     * @param string $file 
     * @return array
     */
    public static function setEnvironment($file = '')
    {
        try {
            $yaml = new Parser();

            if (empty($file)) {
                $file = GLOBAL_PATH.'/Quaver/Config.yml';
            }
            $elements = $yaml->parse(file_get_contents($file));

            // Set DB/Cookie constants
            self::setDatabaseParams($elements['db']);
            self::setCookieParams($elements['cookies']);
            unset($elements['db']);
            unset($elements['cookies']);

            $configObj = (object) $elements;
            $configObj->core['modelPath'] = GLOBAL_PATH.$configObj->core['modelPath'];  
            $configObj->core['controllerPath'] = GLOBAL_PATH.$configObj->core['controllerPath'];

            return $configObj;
            
        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception('Unable to parse the YAML string: %s', $e->getMessage());
        }
    }

    /**
     * Set database constants
     * @param array $params
     */
    private static function setDatabaseParams($params)
    {
        define('DB_HOSTNAME', $params['hostname']? $params['hostname'] : 'localhost'); 
        define('DB_USERNAME', $params['username']? $params['username']: 'root');
        define('DB_PASSWORD', $params['password']? $params['password']: 'root');
        define('DB_DATABASE', $params['database']? $params['database']: 'qv');
        define('DB_PORT', $params['port']? $params['port']: 3306);
        define('DB_DEV_MODE', $params['devMode']? $params['devMode']: true);
        define('CIPHER_KEY', $params['cypherKey']? $params['cypherKey']: '');
    }

    /**
     * Set cookie constants
     * @param array $params
     */
    private static function setCookieParams($params)
    {
        define('COOKIE_NAME', $params['cookieName']? $params['cookieName'] : 'quaver');
        define('COOKIE_DOMAIN', $params['cookieDomain'] != 'server'? $params['cookieDomain']: $_SERVER['HTTP_HOST']);
        define('COOKIE_PATH', $params['cookiePath']? $params['cookiePath'] : '/');
    }
}
