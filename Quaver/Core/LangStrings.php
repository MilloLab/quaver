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

/**
 * LangStrings class.
 */
class LangStrings extends \Quaver\Core\Model
{
    public $_fields = array(
        'id',
        'language',
        'label',
        'text',
    );

    public $_languages;

    protected $table = 'lang_strings'; // sql table


    /**
     * Get language list.
     *
     * @return array
     */
    public static function getList()
    {
        $db = DB::getInstance();
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
                $l = new self();
                $return[] = $l->getFromId($item['id']);
            }
        }

        return $return;
    }

    /**
     * Get string from label.
     *
     * @param mixed $_label
     */
    public function getFromLabel($_label)
    {
        $db = DB::getInstance();
        $_table = $this->table;
        $return = null;

        $item = $db->query("SELECT id FROM $_table WHERE label like '$_label' ORDER BY language");

        $result = $item->fetchAll();

        if ($result) {
            foreach ($result as $item) {
                $l = new self();
                $return[] = $l->getFromId($item['id']);
            }
        }

        return $return;
    }

    /**
     * Save all strings.
     */
    public function saveAll()
    {
        // Other languages
        if (isset($this->_languages)) {
            foreach ($this->_languages as $item) {
                $lang = new self();
                $lang->setItem((array) $item);
                $lang->save();
            }

            return true;
        }
    }

    /**
     * Delete all strings.
     */
    public function deleteAll()
    {
        // Other languages
        if (isset($this->_languages)) {
            foreach ($this->_languages as $item) {
                $lang = new self();
                $lang->setItem((array) $item);
                $lang->delete();
            }

            return true;
        }
    }

    /**
     * Language string setter.
     *
     * @param array $_item
     *
     * @return self[]
     */
    public function setItem($_item)
    {
        foreach ($this->_fields as $field) {
            if (isset($_item[$field])) {
                $this->$field = $_item[$field];
            }
        }

        if (isset($_item['_languages'])) {
            $this->_languages = $_item['_languages'];
        }
    }
}
