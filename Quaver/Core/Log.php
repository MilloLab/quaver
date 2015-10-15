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
 * Log class
 * @package Quaver
 */
class Log extends \Quaver\Core\Model
{
    const SYSTEM_USER = 0;

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_ENABLE = 'enable';
    const ACTION_DISABLE = 'disable';
    const ACTION_PUBLISH = 'publish';
    const ACTION_FINALIZE = 'finalize';
    const ACTION_CHANGE_STATUS = 'change status';
    const ACTION_MOVE = 'move';
    const ACTION_SEND_MESSAGE = 'send message';
    const ACTION_DOWNLOAD = 'download';

    public $_fields = array(
        'id',
        'user',
        'action',
        'model',
        'model_id',
        'model_title',
        'date',
    );

    protected $table = 'log'; // sql table


    /**
     * Notify action.
     *
     * @param object $user
     * @param string $action
     * @param object $model
     *
     * @return bool
     */
    public static function notify($user, $action, $model = null)
    {
        if (!defined('LOG_ENABLED') || !constant('LOG_ENABLED')) {
            return true;
        }

        if ($model instanceof self) {
            return false;
        }

        $log = new self();

        //Set user
        if (is_null($user)) {
            $log->user = self::SYSTEM_USER;
        } elseif (is_object($user)) {
            if (empty($user->id)) {
                return false;
            }
            $log->user = $user->id;
        } elseif (is_array($user)) {
            if (empty($user['id'])) {
                return false;
            }
            $log->user = $log->user['id'];
        } else {
            if (empty($user)) {
                //Probably a login action
                return false;
            }
            $log->user = $user;
        }

        //Set action
        $log->action = $action;

        //Set model
        $log->model = null;

        if (is_object($model)) {
            $model_parts = explode('\\', get_class($model));
            $log->model = end($model_parts);
            $log->model_id = empty($model->id) ? null : $model->id;

            if (!empty($model->slug)) {
                $log->model_title = $model->slug;
            } elseif (!empty($model->id_number)) {
                $log->model_title = $model->id_number;
            } elseif (!empty($model->email)) {
                $log->model_title = $model->email;
            } elseif (!empty($model->action)) {
                $log->model_title = $model->action;
            } elseif (!empty($model->title)) {
                $log->model_title = $model->title;
            } elseif (!empty($model->label)) {
                $log->model_title = $model->label;
            } else {
                $log->model_title = '';
            }
        }

        //Set date
        $log->date = date('Y-m-d H:i:s');

        //Save
        $log->save();

        return !empty($log->id);
    }

    /**
     * Get log list.
     *
     * @return array
     */
    public function getList()
    {
        $db = DB::getInstance();
        $_table = $this->table;

        if (empty($_table)) {
            $_table = 'log';
        }

        $item = $db->query(
            "SELECT l.*, u.email FROM $_table l, user u WHERE l.user=u.id and l.date > DATE_SUB(NOW(), INTERVAL 1 YEAR)"
        );

        $result = $item->fetchAll();

        $return = array();
        if ($result) {
            foreach ($result as $item) {
                $log = new self();
                $log->setItem($item);
                $log->user_email = $item['email'];
                $return[] = $log;
            }
        }

        return $return;
    }
}
