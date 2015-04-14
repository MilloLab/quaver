<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\Core\DB;

/**
 * LangStrings class
 * @package Core
 */
class LangStrings extends \Quaver\Core\Model
{
    public $_fields = array(
        "id",
        "language",
        "label",
        "text",
    );

    public $_languages;

    protected $table = 'lang_strings'; // sql table


    /**
     * Get language list
     * @return type
     */
    public static function getList()
    {
        $db = new DB;
        $_table = 'lang_strings';
        $return = null;

        global $_lang;

        if (isset($_lang->id)) {
            $language = $_lang->id;
        } else {
            $language = 1;
        }

        $item = $db->query("SELECT id FROM $_table WHERE language = $language");

        $result = $item->fetchAll();

        if ($result) {
            foreach ($result as $item) {
                $l = new LangStrings;
                $return[] = $l->getFromId($item['id']);
            }
        }

        return $return;
    }
    
    /**
     * Get string from label
     * 
     * @access public
     * @param mixed $_label
     * @return void
     */
    public function getFromLabel($_label)
    {
        $db = new DB;        
        $_table = $this->table;
        $return = null;

        $item = $db->query("SELECT id FROM $_table WHERE label like '$_label' ORDER BY language");

        $result = $item->fetchAll();

        if ($result) {
            foreach ($result as $item) {
                $l = new LangStrings;
                $return[] = $l->getFromId($item['id']);
            }
        }

        return $return;
    }
    
   
    
    /**
     * Save all strings
     * 
     * @access public
     * @return void
     */
    public function saveAll()
    {
        // Other languages
        if (isset($this->_languages)) {
            foreach ($this->_languages as $item) {
                $lang = new LangStrings;
                $lang->setItem((array)$item);
                $lang->save();
            }

            return true;
        }
    }

    /**
     * Delete all strings
     * 
     * @access public
     * @return void
     */
    public function deleteAll()
    {
        // Other languages
        if (isset($this->_languages)) {
            foreach ($this->_languages as $item) {
                $lang = new LangStrings;
                $lang->setItem((array)$item);
                $lang->delete();
            }

            return true;
        }  
    }
    

    /**
     * Language string setter
     * @param type $_item 
     * @return type
     */
    public function setItem($_item)
    {
        foreach ($this->_fields as $field) {
            if (isset($_item[$field])){
                $this->$field = $_item[$field];
            }
        }

        if (isset($_item['_languages'])) {
            $this->_languages = $_item['_languages'];
        }
    }
}
