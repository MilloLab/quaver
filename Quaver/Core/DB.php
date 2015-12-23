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

use PDO;

/**
 * DB class.
 */
class DB
{
    private $conn = null;
    private static $instance = null;

    private function __construct()
    {
    }

    public function __clone()
    {
    }

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

                    if ($config->db['debug'] === true) {
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
