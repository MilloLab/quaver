<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

use Quaver\Core\DB;
use Quaver\Core\Model;

/**
 * User class.
 */
class User extends Model
{
    public $_fields = array(
      'id',
      'active',
      'level',
      'email',
      'password',
      'dateRegister',
      'dateLastLogin',
    );

    public $cookie = '';
    public $logged = false;

    protected $table = 'user'; // sql table


    /**
     * Get users list.
     */
    public function getList()
    {
        try {
            $db = new DB();
            $_table = $this->table;
            $return = false;

            $item = $db->query("SELECT id FROM $_table");

            $result = $item->fetchAll();

            if ($result) {
                foreach ($result as $item) {
                    $user = new self();
                    $return[] = $user->getFromId($item['id']);
                }
            }

            return $return;
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        if ($this->id > 0) {
            return ($this->active == 1);
        }
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        if ($this->id > 0) {
            return ($this->level == 'admin');
        }
    }

    /**
     * Cookie setter.
     *
     * @param string $_cookie (default: '')
     */
    public function setCookie($_cookie = '')
    {
        if (!empty($_cookie)) {
            $this->cookie = $_cookie;
        }

        if (!empty($this->cookie)) {
            setCookie(COOKIE_NAME.'_log', $this->cookie, time() + 60 * 60 * 24 * 30, COOKIE_PATH, COOKIE_DOMAIN);
        }
    }

    /**
     * Unset user cookie.
     */
    public function unsetCookie()
    {
        setCookie(COOKIE_NAME.'_log', '', time() - 1, COOKIE_PATH, COOKIE_DOMAIN);
        setCookie('PHPSESSID', '', time() - 1, COOKIE_PATH);

        $this->logged = false;
    }

    /**
     * Create new cookie.
     */
    public function cookie()
    {
        if (empty($this->cookie) && !empty($this->id)) {
            $this->cookie = sha1($this->email.md5($this->id));
        }

        return $this->cookie;
    }

    /**
     * Get user from cookie.
     *
     * @param mixed $_cookie
     */
    public function getFromCookie($_cookie)
    {
        try {
            $db = new DB();

            $this->cookie = substr($_cookie, 0, 40);
            $_table = $this->table;
            $_cookieSet = $this->cookie;

            $id = $db->query("
              SELECT id
              FROM $_table
              WHERE SHA1(CONCAT(email, MD5(id))) = '$_cookieSet'");

            $result = $id->fetchColumn(0);

            if ($result > 0) {
                $this->getFromId($result);
                if (!$this->isActive()) {
                    $this->unsetCookie();
                } else {
                    $this->logged = true;
                }
                $return = $this->id;
                $this->updateLastLogin();
            } else {
                $this->unsetCookie();
                $return = false;
            }

            return $return;
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * Update last login (date).
     */
    public function updateLastLogin()
    {
        if ($this->id > 0) {
            $this->dateLastLogin = date('Y-m-d H:i:s', time());
            $this->save();
        }
    }

    /**
     * Hash user password.
     */
    public function hashPassword($_pass)
    {
        if (!empty($_pass)) {
            return md5(sha1($_pass));
        }
    }

    /**
     * Check if email is registered.
     *
     * @param mixed $_email
     */
    public function isEmailRegistered($_email)
    {
        try {
            $db = new DB();
            $_table = $this->table;
            $return = null;

            $item = $db->query("SELECT id
                  FROM $_table
                  WHERE email = '$_email'");

            $result = $item->fetchColumn(0);

            if ($result) {
                $return = true;
            } else {
                $return = false;
            }

            return $return;
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
     * Get user from email and password.
     *
     * @param mixed $_email
     * @param mixed $_password
     */
    public function getFromEmailPassword($_email, $_password)
    {
        try {
            $db = new DB();
            $_table = $this->table;
            $return = null;

            if (!empty($_email) && !empty($_password)) {
                $item = $db->query("
                    SELECT id
                    FROM $_table
                    WHERE email = '".$_email."'
                    AND password  = MD5(SHA1('".$_password."'))");

                $result = $item->fetchColumn(0);

                if ($result > 0) {
                    $this->getFromId($result);
                    $this->cookie();
                    $this->logged = true;
                    $this->updateLastLogin();
                    $return = $this->id;
                } else {
                    $return = false;
                }
            }

            return $return;
        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }
}
