<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\Core\DB;

/**
 * Model class
 * @package Core
 */
abstract class Model
{

    public $id,
        $language;

    protected $table;

    /**
     * Get language when start the object 
     * @return type
     */
    public function __construct()
    {
        if (isset($GLOBALS['_lang'])) {
            $this->language = $GLOBALS['_lang']->id;    
        }
    }

    /**
     * Setter
     * @param type $_item 
     * @return type
     */
    public function setItem($_item)
    {
        foreach ($this->_fields as $field) {
            if (isset($_item[$field])) {
                $this->$field = $_item[$field];
            }
        }
    }

    /**
     * Getter
     * @return type
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
     * Get object from ID
     * @param type $_id 
     * @return type
     */
    public function getFromId($_id)
    {
        $db = new DB;
        $_id = (int)$_id;
        $_table = $this->table;

        $item = $db->query("SELECT * FROM $_table WHERE id = '$_id'");

        $result = $item->fetchAll();

        if ($result) {
            $this->setItem($result[0]);
        }

        return $this;
    }

    /**
     * Save data to DB
     * @return type
     */
    public function save()
    {

        try {

            $db = new DB;

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
                $sql = "INSERT INTO $_table SET " . $set;
            } else {
                $values[':id'] = $this->id;
                $sql = "UPDATE $_table SET " . $set . " WHERE id = :id";
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
     * Delete object (DB) 
     * @return type
     */
    public function delete()
    {

        try {

            $db = new DB;

            $_id = (int)$this->id;
            $_table = $this->table;

            $sql = "DELETE FROM $_table WHERE id = :id";
            if ($db->query($sql, array(':id'=>$_id))) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * Convert all to Array
     * @return type
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
     * Encode to JSON
     * @return type
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
