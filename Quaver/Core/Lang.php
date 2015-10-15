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
 * Lang class
 * @package Quaver
 */
class Lang extends \Quaver\Core\Model
{
    public $_fields = array(
        'id',
        'name',
        'large',
        'slug',
        'locale',
        'active',
        'priority',
    );

    public $strings;
    protected $table = 'lang';
    protected $table_strings = 'lang_strings';

    /**
     * Get object from ID.
     *
     * @param ind $_id
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
     * Get site language.
     * @param int $defaultLang
     * @param bool $forcedLang
     * @return self[]
     */
    public function getSiteLanguage($defaultLang = 1, $forcedLang = false)
    {
        $return = $this->getLanguageFromCookie();

        if (!$return) {
            
            if ($forcedLang) {
                $this->getFromId($defaultLang);
            } else {
                $language_slug = \Quaver\Core\Helper::getBrowserLanguage();
                $this->getFromSlug($language_slug, true);
            }
            
            if (empty($this->slug)) {
                $this->getFromId($defaultLang);
            }

            $return = $this;
        }

        return $return;
    }

    /**
     * Get language from cookie.
     *
     * @return self[]
     */
    public function getLanguageFromCookie()
    {
        $config = Config::getInstance();

        $return = false;
        if (!empty($_COOKIE[$config->cookies['cookieName'].'_lang'])) {
            $language = $_COOKIE[$config->cookies['cookieName'].'_lang'];
            $return = $this->getFromId($language);
        }

        return $return;
    }

    /**
     * Language cookie setter.
     */
    public function setCookie()
    {
        $config = Config::getInstance();

        if (!empty($this->id)) {
            setcookie($config->cookies['cookieName'].'_lang',
                      $this->id,
                      time() + 60 * 60 * 24 * 7,
                      $config->cookies['cookiePath'],
                      $config->cookies['cookieDomain']);
        }
    }

    /**
     * Get object from slug.
     *
     * @param string $_slug
     * @param bool $_short
     * @param int $defaultLang
     * @return object
     */
    public function getFromSlug($_slug, $_short = false, $defaultLang = 1)
    {
        $db = DB::getInstance();

        $return = $defaultLang;
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
     * Get all languages.
     *
     * @return array
     */
    public function getLanguages()
    {
        $db = DB::getInstance();
        $return = null;
        $_table = 'lang';

        $items = $db->query("SELECT * FROM $_table WHERE active = 1 ORDER BY id ASC");
        $result = $items->fetchAll();

        foreach ($result as $l) {
            $ob_lang = new self();
            $return[] = $ob_lang->getFromId($l['id']);
        }

        return $return;
    }

    /**
     * Get languages list.
     *
     * @param bool $_all
     * @param bool $_byPriority
     *
     * @return object
     */
    public static function getList($_all = true, $_byPriority = false)
    {
        $db = DB::getInstance();
        $return = null;
        $_table = 'lang';

        $where = '';
        $order = '';

        if ($_byPriority) {
            $order = 'ORDER BY priority ASC';
        }

        if ($_all) {
            $where = 'WHERE active = 1';
        }

        $items = $db->query("SELECT id FROM $_table $where $order");

        if ($items) {
            $result = $items->fetchAll();
            foreach ($result as $item) {
                $ob_lang = new self();
                $return[] = $ob_lang->getFromId($item['id']);
            }
        }

        return $return;
    }

    /**
     * Convert string to real text.
     *
     * @param string $_label
     * @param string $_utf8
     *
     * @return string
     */
    public function typeFormat($_label, $_utf8 = '')
    {
        if (isset($this->strings[$_label]) && !empty($this->strings[$_label])) {
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
            $newString = new LangStrings();
            $newString->language = $this->id;
            $newString->label = $_label;
            $newString->text = $return;

            if ($newString->save()) {
                $this->strings[$_label] = $return;
            }
        }

        return $return;
    }

    /**
     * TypeFormat shortener.
     *
     * @param string $_label
     *
     * @return string
     */
    public function l($_label)
    {
        return $this->typeFormat($_label, 'd');
    }
}
