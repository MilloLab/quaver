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

/**
 * Model class
 * @package Quaver
 */
abstract class Model
{
    public $id,
        $language;

    protected $table;

    /**
     * Get language when start the object.
     */
    public function __construct()
    {
        if (isset($GLOBALS['_lang'])) {
            $this->language = $GLOBALS['_lang']->id;
        }
    }

    /**
     * Setter.
     *
     * @param array $_item
     *
     */
    public function setItem($_item)
    {
        foreach ($this->_fields as $field) {
            if (array_key_exists($field, $_item)) {
                $this->$field = $_item[$field];
            }
        }
    }

    /**
     * Getter.
     *
     * @return array
     */
    public function getItem()
    {
        $item = array();
        foreach ($this->_fields as $field) {
            $item[$field] = $this->$field;
        }

        return $item;
    }

    /**
     * Get values by custom select
     * @param array|string $_value 
     * @param array|string $_where 
     * @param string $_order 
     * @return array
     */
    public function getValues($_value, $_where = '', $_order = '')
    {
        $db = DB::getInstance();
        $_table = $this->table;

        // Set params
        $values = '';
        if (is_array($_value)) {
            foreach ($_value as $field) {
                if ($values != '') {
                    $values .= ', ';
                }

                $values .= "$field";
            }
        } else {
            $values = $_value;
        }

        // Set conditions
        $where = '';
        $params = '';
        if (is_array($_where)) {
            foreach ($_where as $key => $field) {
                $where[] = "$key = :$key";
                $params[":$key"] = $field;
            }
            $where = count($where) ? 'WHERE ('.implode(') AND (', $where).')' : '';
        } else {
            $where = "WHERE $_where";
            $params = ':$_where';
        }

        // Set order
        $order = "ORDER BY $_order";
        
        try {     
            $sql = "SELECT $values FROM $_table $where $order";
            $items = $db->query($sql, $params);
            return $items->fetchAll();

        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }

    }

    /**
     * Get object from ID.
     *
     * @param int $_id
     *
     * @return self[]
     */
    public function getFromId($_id)
    {
        $db = DB::getInstance();
        $_id = (int) $_id;
        $_table = $this->table;

        $item = $db->query("SELECT * FROM $_table WHERE id = '$_id'");

        $result = $item->fetchAll();

        if ($result) {
            $this->setItem($result[0]);
        }

        return $this;
    }

    /**
     * Get array list
     * 
     * @return array
     */
    public function getArrayList()
    {
        $_table = $this->table;
        if (!$_table) {
            return false;
        }

        $db = new DB();
        $return = null;

        $item = $db->query("SELECT id FROM $_table");
        $result = $item->fetchAll();

        if ($result) {
            foreach ($result as $item) {
                $class = new self();
                $return[] = $class->getFromId($item['id']);
            }
        }

        return $return;
    }

    /**
     * Save data to DB.
     *
     * @return bool
     */
    public function save()
    {
        try {
            $db = DB::getInstance();

            $set = '';
            $values = array();
            $_table = $this->table;

            foreach ($this->_fields as $field) {
                if ($set != '') {
                    $set .= ', ';
                }

                $set .= "`$field` = :$field";
                $values[":$field"] = $this->$field;
            }

            if (empty($this->id)) {
                $sql = "INSERT INTO $_table SET ".$set;
            } else {
                $values[':id'] = $this->id;
                $sql = "UPDATE $_table SET ".$set.' WHERE id = :id';
            }

            $db->query($sql, $values);

            if (empty($this->id)) {
                $this->id = $db->insertId();
            }

            return true;
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * Delete object (DB).
     *
     * @return bool
     */
    public function delete()
    {
        try {
            $db = DB::getInstance();

            $_id = (int) $this->id;
            $_table = $this->table;

            $sql = "DELETE FROM $_table WHERE id = :id";
            if ($db->query($sql, array(':id' => $_id))) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * Convert all to Array.
     *
     * @return array|bool
     */
    public function toArray()
    {
        $return = false;

        if (isset($this->_fields)) {
            foreach ($this->_fields as $field) {
                $return[$field] = $this->$field;
            }
        }

        if (isset($this->_fields_extra)) {
            foreach ($this->_fields_extra as $field) {
                $return[$field] = $this->$field;
            }
        }

        return $return;
    }

    /**
     * Encode to JSON.
     *
     * @return string|bool
     */
    public function toJson()
    {
        if ($this->toArray()) {
            $return = json_encode($this->toArray());
        } else {
            $return = false;
        }

        return $return;
    }
}
