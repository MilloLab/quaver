<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use PDO;

class DB
{
    private $conn = null;
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
     * setConnection.
     */
    public function setConnection()
    {
        $this->conn = $this->getConnection();
    }

    /**
     * getConnection.
     *
     * @return object
     */
    public function getConnection()
    {
        static $conn;

        if (!$conn) {

            $config = Config::getInstance();

            // Connecting to mysql
            if (!$config->db['hostname']
                || !$config->db['username']
                || !$config->db['database']
                || !$config->db['port']
            ) {
                die('Database parameters needed.');
            } else {
                try {
                    // Config mysql link
                    $conn = new PDO('mysql:host='.$config->db['hostname'].';dbname='.$config->db['database'].';port='.$config->db['port'], $config->db['username'], $config->db['password']);
                    $conn->exec('SET CHARACTER SET utf8');

                    if ($config->db['devMode'] === true) {
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    }
                } catch (\PDOException $e) {
                    throw new \Quaver\Core\Exception($e->getMessage());
                }
            }
        }

        return $conn;
    }

    /**
     * @param $query
     * @param null $params
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function query($query, $params = null)
    {
        $db = $this->getConnection();

        $params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);

        $result = $db->prepare($query);

        try {
            $result->execute($params);

            return $result;
        } catch (\PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function insertId()
    {
        try {
            return $this->query('SELECT LAST_INSERT_ID();')->fetchColumn();
        } catch (\Quaver\Core\Exception $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }
}
