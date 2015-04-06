<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

use Quaver\Core\DB;

class Lang extends \Quaver\Core\Model
{

    public $_fields = array(
        "id",
        "name",
        "slug",
        "locale",
        "active",
        "priority",
    );

    public $strings;
    protected $table = 'lang';
    protected $table_strings = 'lang_strings';

    /**
     * @param $_id
     * @return $this
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
            
            $_table_strings = $this->table_strings;
            $_idSet = $this->id;

            $strings = $db->query("SELECT * FROM $_table_strings WHERE language = '$_idSet'");
            $resultLang = $strings->fetchAll();

            if ($resultLang) {
                foreach ($resultLang as $string) {
                    if (!isset($this->strings[$string['label']])) {
                        $this->strings[$string['label']] = utf8_encode($string['text']);
                    }
                }
            }
        }

        return $this;
    }


    /**
     * @return $this|bool|lang
     */
    public function getSiteLanguage()
    {

        $return = $this->getLanguageFromCookie();

        if (!$return) {

            if (defined('LANG_FORCE')) {
                
                if (LANG_FORCE === true) {
                    $this->getFromId(LANG);
                } else {
                    $language_slug = \Quaver\Core\Helper::getBrowserLanguage();
                    $this->getFromSlug($language_slug, true);
                }
            }

            if (empty($this->slug)) {
                $this->getFromId(LANG);
            }

            $return = $this;

        }        

        return $return;
    }

    /**
     * getLanguageFromCookie
     * @return type
     */
    public function getLanguageFromCookie()
    {
        $return = false;
        if (!empty($_COOKIE[COOKIE_NAME . "_lang"])) {
            $language = $_COOKIE[COOKIE_NAME . "_lang"];
            $return = $this->getFromId($language);
        }

        return $return;
    }

    /**
     * setCookie
     * Set language cookie
     * @return type
     */
    public function setCookie()
    {
        if (!empty($this->id)) {
            setcookie(COOKIE_NAME . "_lang",
                      $this->id,
                      time()+60*60*24*7,
                      COOKIE_PATH,
                      COOKIE_DOMAIN);
        }
    }

    /**
     * getFromSlug
     * @param type $_slug 
     * @param type $_short 
     * @return type
     */
    public function getFromSlug($_slug, $_short = false)
    {
        $db = new DB;

        $return = LANG;
        $_table = $this->table;

        $slug_where = 'slug';
        if ($_short) {
            $slug_where = 'SUBSTR(slug, 1, 2)';
        }

        $_slug = substr($_slug, 0, 3);
        $language = $db->query("SELECT id FROM $_table WHERE $slug_where = '$_slug' AND active = 1");
        $resultLang = $language->fetchColumn(0);

        if ($resultLang) {
            $this->getFromId($resultLang);
            $return = $this;
        }

        return $return;
    }

    /**
     * getLanguages (object array)
     * @return type
     */
    public function getLanguages()
    {
        $db = new DB;
        $return = null;
        $_table = 'lang';

        $items = $db->query("SELECT * FROM $_table ORDER BY id ASC");
        $result = $items->fetchAll();

        foreach ($result as $l) {
            $ob_lang = new Lang;
            $return[] = $ob_lang->getFromId($l['id']);
        }

        return $return;
    }

    /**
     * getList
     * @param type $_all 
     * @param type $_byPriority 
     * @return type
     */
    public static function getList($_all = false, $_byPriority = false)
    {

        $db = new DB;
        $return = null;
        $_table = 'lang';

        $where = '';
        $order = '';

        if ($_byPriority) {
            $order = 'ORDER BY priority ASC';
        }

        if ($_all) {
            $where = "WHERE active = 1";
        }

        $items = $db->query("SELECT id FROM $_table $where $order");

        if ($items){
            $result = $items->fetchAll();
            foreach ($result as $item) {
                $ob_lang = new Lang;
                $return[] = $ob_lang->getFromId($item['id']);
            }
        }

        return $return;
    }

    /**
     * @param $_label
     * @param string $_utf8
     * @return string
     */
    public function typeFormat($_label, $_utf8 = '')
    {
        if (isset($this->strings[$_label])) {
            $return = $this->strings[$_label];
            switch ($_utf8) {
                case('d'):
                    $return = utf8_decode($return);
                    break;
                case('e'):
                    $return = utf8_encode($return);
                    break;
            }
        } else {
            $return = "#$_label#";
        }

        return $return;
    }

    /**
     * @param $_label
     * @return string
     */
    public function l($_label)
    {
        return $this->typeFormat($_label, 'd');
    }

}
