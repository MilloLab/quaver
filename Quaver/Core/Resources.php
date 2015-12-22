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
 * Resources class.
 */
class Resources
{
    /**
     * Constructor.
     *
     * @param array $arrayPath
     * @param bool  $devMode
     */
    public function __construct($arrayPath, $devMode = true)
    {
        $this->add($arrayPath);
        $this->devMode = $devMode;
    }

    /**
     * Add values to object.
     *
     * @param array $values
     */
    protected function add($values)
    {
        if (isset($values)) {
            foreach ($values as $k => $v) {
                $this->$k = $v;
            }
        }
    }

    /**
     * Set css, js and img files.
     *
     * @param string $file
     * @param string $type
     * @param bool   $_randomVar
     *
     * @return mixed
     */
    public function map($file, $type, $_randomVar = true)
    {
        if (isset($file) && isset($type)) {
            $part = explode($type, $file);
            if ($type == 'css' || $type == 'js') {
                $min = !$this->devMode ? 'min.' : '';
            } else {
                $min = '';
            }
            $randomVar = $_randomVar ? '?'.$this->randomVar : '';
            $typePath = isset($this->$type) ? $this->$type : $this->img;

            if ($type == 'css' || $type == 'js') {
                if (!empty($min)) {
                    if (!file_exists(GLOBAL_PATH . $typePath.'/'.$part[0].$min.$type)) {
                        $min = '';
                    }    
                } else {
                    if (!file_exists(GLOBAL_PATH . $typePath.'/'.$part[0].$type)) {
                        $min = 'min.';
                    }
                }
            }

            return $typePath.'/'.$part[0].$min.$type.$randomVar;
        } else {
            return false;
        }
    }
}
