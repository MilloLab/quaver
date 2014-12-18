<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

/**
 * Class Helper
 */
class Helper
{
    
    /**
     * getBrowserLanguage
     * @return type
     */
    public static function getBrowserLanguage()
    {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    /**
    * cipher function.
    * 
    * @access public
    * @return void
    */
    public static function cipher($_value) {
        $_value = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, CIPHER_KEY, $_value, MCRYPT_MODE_ECB));
        return $_value;
    }

    /**
    * decipher function.
    * 
    * @access public
    * @return void
    */
    public static function decipher($_value) {
        $decode = base64_decode($_value);
        $_value = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, CIPHER_KEY, $decode, MCRYPT_MODE_ECB));
        return $_value;
    }

    /**
     * clearInjection
     * @param type $val 
     * @param type $post 
     * @return type
     */
    public static function clearInjection($val, $post = false)
    {
        if ($post) {
            $val = str_ireplace("SELECT","",$val);
            $val = str_ireplace("COPY","",$val);
            $val = str_ireplace("DELETE","",$val);
            $val = str_ireplace("DROP","",$val);
            $val = str_ireplace("DUMP","",$val);
            $val = str_ireplace(" OR ","",$val);
            $val = str_ireplace("LIKE","",$val);
        } else {
            $val = str_ireplace("SELECT","",$val);
            $val = str_ireplace("COPY","",$val);
            $val = str_ireplace("DELETE","",$val);
            $val = str_ireplace("DROP","",$val);
            $val = str_ireplace("DUMP","",$val);
            $val = str_ireplace(" OR ","",$val);
            $val = str_ireplace("%","",$val);
            $val = str_ireplace("LIKE","",$val);
            $val = str_ireplace("--","",$val);
            $val = str_ireplace("^","",$val);
            $val = str_ireplace("[","",$val);
            $val = str_ireplace("]","",$val);
            $val = str_ireplace("\\","",$val);
            $val = str_ireplace("!","",$val);
            $val = str_ireplace("¡","",$val);
            $val = str_ireplace("?","",$val);
            $val = str_ireplace("=","",$val);
            $val = str_ireplace("&","",$val);
        }

        return $val;
    }

    /**
    * formatPercentage
    * @param type $n 
    * @return type
    */
    public static function formatPercentage($n)
    {
        $n = str_replace(',','.', $n);
        return $n;
    }

    /**
    * @param $_str
    * @return string
    */
    public static function cleanInt($s)
    {
        $s = str_replace('"','', $s);
        $s = str_replace(':','', $s);
        $s = str_replace('.','', $s);
        $s = str_replace(',','', $s);
        $s = str_replace(';','', $s);

        return $s;
    }
}

