<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Model;
use Quaver\Core\DB;

/**
 * user_default class.
 * 
 * @extends base_object
 */
class UserDefault extends Base
{
  public $_fields = array(
      "id",
      "active",
      "level", 
      "email",
      "name",
      "password",
      "dateRegister",
      "dateLastLogin",
      "language"
  );

  public $cookie = '';
  public $logged = false;
  
  public $table = 'user'; // sql table

  /**
   * isActive function.
   * 
   * @access public
   * @return void
   */
  public function isActive() 
  {  
    return ($this->active == 1);
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
    if (!empty($_cookie)) $this->cookie = $_cookie;
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
    $db = new DB;

    $this->cookie = substr($_cookie, 0, 40);

    $id = $db->query("
        SELECT id
        FROM " . $this->table . "
            WHERE SHA1(CONCAT(email, MD5(id))) = '" . $this->cookie . "'");

    $result = $id->fetchColumn(0);

    if ($result > 0) {
        $this->getFromId($result);
        if (!$this->isActive()){
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
  }

  /**
   * updateLastLogin function.
   * 
   * @access public
   * @return void
   */
  public function updateLastLogin() {
    if ($this->id > 0) {
        $this->dateLastLogin = time();
        $this->save();
    }
  }

  /**
   * @return bool
   */
  public function save()
  {
    try {

      $db = new DB;

      $set = '';
      $values = array();

      foreach ($this->_fields as $field) {
          if ($set != '') $set .= ', ';
          $set .= "$field = :$field";
          $values[":$field"] = $this->$field;
      }

      if(empty($this->id)){
          $sql = "INSERT INTO " . $this->table . " SET " . $set;

      } else {
          $values[':id'] = $this->id;
          $sql = "UPDATE " . $this->table . " SET " . $set . " WHERE id = :id";
      }

      $db->query($sql, $values);

      if (empty($this->id)){
          $this->id = $db->insertId();
      }

      return true;

    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }
  }

}	
?>