<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Model;
use Quaver\Core\DB;

/**
 * Class UserDefault
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
    $_table = $this->table;
    $_cookieSet = $this->cookie;

    $id = $db->query("
        SELECT id
        FROM $_table
        WHERE SHA1(CONCAT(email, MD5(id))) = '$_cookieSet'");

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

}	
?>
