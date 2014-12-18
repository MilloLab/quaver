<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use \PDO;

/**
 * Class DB
 */
class DB
{

    public $conn = null;

    /**
     * constructor
     */
    public function __construct()
    {

        // Connecting to mysql
        if (!defined('DB_USERNAME')
            || !defined('DB_PASSWORD')
            || !defined('DB_DATABASE')
        ) {
            die('Database parameters needed.');

        } else {

            try {
                // Config mysql link
                $this->conn = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE, DB_USERNAME, DB_PASSWORD);
                $this->conn->exec('SET CHARACTER SET utf8');

                if (defined('DEV_MODE') && DEV_MODE) {
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }

            } catch (\PDOException $e) {
                throw new \Quaver\Core\Exception($e->getMessage());
            }

        }

    }

    /**
     * @param $query
     * @param null $params
     * @return mixed
     * @throws Exception
     */
    public function query($query, $params = null)
    {

        static $db = null;

        if ($db === null) {
            $db = $this->conn;
        }

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
    public static function insertId()
    {
        try {
            return self::query("SELECT LAST_INSERT_ID();")->fetchColumn();
        } catch (\Quaver\Core\Exception $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }
}
