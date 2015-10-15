<?php
/**
 * Quaver Framework
 *
 * @author      Alberto González <quaver@millolab.com>
 * @copyright   2014 Alberto González (Based on AwesomezGuy autoloader)
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

namespace Quaver;

/**
 * load file.
 *
 * @param string $namespace
 *
 * @return mixed
 */
function load($namespace)
{
    $splitpath = explode('\\', $namespace);
    $path = '';
    $name = '';
    $firstword = true;
    $countSplitPath = count($splitpath);

    for ($i = 0; $i < $countSplitPath; $i++) {
        if ($splitpath[$i] && !$firstword) {
            if ($i == count($splitpath) - 1) {
                $name = $splitpath[$i];
            } else {
                $path .= DIRECTORY_SEPARATOR.$splitpath[$i];
            }
        }
        if ($splitpath[$i] && $firstword) {
            if ($splitpath[$i] != __NAMESPACE__) {
                break;
            }
            $firstword = false;
        }
    }
    if (!$firstword) {
        $fullpath = __DIR__.'/..'.$path.DIRECTORY_SEPARATOR.$name.'.php';

        if (!file_exists($fullpath)) {
            $fullpath = __DIR__.'/../..'.$path.DIRECTORY_SEPARATOR.$name.'.php';   
        }

        return include_once $fullpath;
        
    }

    return false;
}

/**
 * loadPath.
 *
 * @param string $absPath
 *
 */
function loadPath($absPath)
{
    return include_once $absPath;
}

spl_autoload_register(__NAMESPACE__.'\load');
