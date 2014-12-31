<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

use Quaver\Core\DB;
use Quaver\Core\Model;

class User extends Model
{
    public $_fields = array(
      "id",
      "active",
      "level", 
      "email",
      "password",
      "dateRegister",
      "dateLastLogin"
    );

    public $cookie = '';
    public $logged = false;

    public $table = 'user'; // sql table


    /**
    * getList function.
    * 
    * @access public
    * @return void
    */
    public function getList()
    {
        try {

            $db = new DB;
            $_table = $this->table;
            $return = NULL;

            $item = $db->query("SELECT id FROM $_table");

            $result = $item->fetchAll();

            if ($result) {
                foreach ($result as $item) {
                    $user = new User;
                    $return[] = $user->getFromId($item['id']);
                }
            }

            return $return;

        } catch (PDOException $e) {
            throw new \Quaver\Core\Exception($e->getMessage());
        }
    }

    /**
    * isActive function.
    * 
    * @access public
    * @return void
    */
    public function isActive() 
    {
        if ($this->id > 0) {
            return ($this->active == 1);
        }
    }

    /**
    * isAdmin function.
    * 
    * @access public
    * @return void
    */
    public function isAdmin()
    {
        if ($this->id > 0) {
            return ($this->level == "admin");    
        }
        
    }

    /**
    * setCookie function.
    * 
    * @access public
    * @param string $_cookie (default: '')
    * @return void
    */
    public function setCookie($_cookie = '')
    {
        if (!empty($_cookie)) {
            $this->cookie = $_cookie;
        }

        if (!empty($this->cookie)) {
            setCookie(COOKIE_NAME . "_log", $this->cookie, time() + 60 * 60 * 24 * 30, COOKIE_PATH, COOKIE_DOMAIN);    
        }
    }

    /**
    * unsetCookie function.
    * 
    * @access public
    * @return void
    */
    public function unsetCookie()
    {
        setCookie(COOKIE_NAME . "_log", "", time()-1, COOKIE_PATH, COOKIE_DOMAIN);
        setCookie("PHPSESSID", "", time()-1, COOKIE_PATH);

        $this->logged = false; 
    }

    /**
    * cookie function.
    * 
    * @access public
    * @return void
    */
    public function cookie()
    {
        if (empty($this->cookie) && !empty($this->id)) {
            $this->cookie = sha1($this->email . md5($this->id));
        }

        return $this->cookie;
    }

    /**
    * getFromCookie function.
    * 
    * @access public
    * @param mixed $_cookie
    * @return void
    */
    public function getFromCookie($_cookie)
    {
        try {

            $db = new DB;

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
    * updateLastLogin function.
    * 
    * @access public
    * @return void
    */
    public function updateLastLogin()
    {
        if ($this->id > 0) {
            $this->dateLastLogin = date('Y-m-d H:i:s', time());
            $this->save();
        }
    }

    /**
    * hashPassword function.
    * 
    * @access public
    * @return void
    */
    public function hashPassword($_pass)
    {
        if (!empty($_pass)){
            return md5(sha1($_pass));  
        } 
    }

    /**
    * isEmailRegistered function.
    * 
    * @access public
    * @param mixed $_email
    * @return void
    */
    public function isEmailRegistered($_email)
    {
        try {

            $db = new DB;
            $_table = $this->table;
            $return = NULL;

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
    * getFromEmailPassword function.
    * 
    * @access public
    * @param mixed $_email
    * @param mixed $_password
    * @return void
    */
    public function getFromEmailPassword($_email, $_password)
    {
        try {

            $db = new DB;
            $_table = $this->table;
            $return = NULL;

            if (!empty($_email) && !empty($_password)){

                $item = $db->query("
                    SELECT id
                    FROM $_table
                    WHERE email = '" . $_email . "'
                    AND password  = MD5(SHA1('" . $_password . "'))");

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
