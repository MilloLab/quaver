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

    /**
     * constructor.
     */
    public function __construct()
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

            // Connecting to mysql
            if (!defined('DB_USERNAME')
                || !defined('DB_PASSWORD')
                || !defined('DB_DATABASE')
                || !defined('DB_PORT')
            ) {
                die('Database parameters needed.');
            } else {
                try {
                    // Config mysql link
                    $conn = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE.';port='.DB_PORT, DB_USERNAME, DB_PASSWORD);
                    $conn->exec('SET CHARACTER SET utf8');

                    if (defined('DB_DEV_MODE') && DB_DEV_MODE === true) {
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
