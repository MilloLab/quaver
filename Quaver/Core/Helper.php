<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

/**
 * Helper class
 * @package Core
 */
class Helper
{
    
    /**
     * Get browser language
     * @return type
     */
    public static function getBrowserLanguage()
    {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    /**
     * Function to get the client IP address
     * @return type
     */
    public static function getClientIP()
    {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP']) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if ($_SERVER['HTTP_X_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if ($_SERVER['HTTP_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if ($_SERVER['HTTP_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if ($_SERVER['REMOTE_ADDR']) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
    
    /**
     * Cipher data with CYPHER_KEY constant and mcrypt
     * @param type $_value 
     * @return type
     */
    public static function cipher($_value) {
        $_value = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, CIPHER_KEY, $_value, MCRYPT_MODE_ECB));
        return $_value;
    }

    /**
     * Decipher data with CYPHER_KEY constant and mcrypt
     * @param type $_value 
     * @return type
     */
    public static function decipher($_value) {
        $decode = base64_decode($_value);
        $_value = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, CIPHER_KEY, $decode, MCRYPT_MODE_ECB));
        return $_value;
    }

    /**
     * Translate actual day name to spanish
     * @return type
     */
    public static function getDay()
    {
        $day = date("l");

        if (isset($GLOBALS['_lang'])) {
            
            if ($GLOBALS['_lang'] ==  2) {

                switch ($day) {
                    case "Monday":
                        $day = "Lunes";
                        break;
                    case "Tuesday":
                        $day = "Martes";
                        break;
                    case "Wednesday":
                        $day = "Miércoles";
                        break;
                    case "Thursday":
                        $day = "Jueves";
                        break;
                    case "Friday":
                        $day = "Viernes";
                        break;
                    case "Saturday":
                        $day = "Sábado";
                        break;
                    case "Sunday":
                        $day = "Domingo";
                        break;
                    default:
                        $day = "Unknown";
                        break;
                }
            }
        }

        return $day;
    }

    /**
     * Change date to spanish format
     * @param type $_time 
     * @return type
     */
    public static function formatDate($_time)
    {    
        return date("d-m-Y H:i:s", $_time);
    }

    /**
     * Change percentage to spanish format
     * @param type $n 
     * @return type
     */
    public static function formatPercentage($n)
    {
        $n = str_replace(',','.', $n);
        return $n;
    }

    /**
     * (Legacy) Clean integers puntuation
     * @param type $s 
     * @return type
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

    /**
     * (Legacy) Clean format string
     * @param type $_str 
     * @return type
     */
    public static function cleanString($_str)
    {
        // Change characteres...
        $i = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í',
            'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß',
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï',
            'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă',
            'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē',
            'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ',
            'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ',
            'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń',
            'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ',
            'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť',
            'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ',
            'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ',
            'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ',
            'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', '!', '?', '\\', '.', '&', ',', ':', '(', ')', ';', '^', '¡', '¿', '//', '"', '@');
        // ...in this other...
        $o = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I',
            'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i',
            'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A',
            'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E',
            'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G',
            'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ',
            'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N',
            'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r',
            'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't',
            'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w',
            'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A',
            'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A',
            'a', 'AE', 'ae', 'O', 'o', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
        $str = str_replace($i, $o, $_str);
        // Replace more
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -_\/]/', '/[ -]+/', '/[ _]+/', '/[ \/]+/', '/^-|-$/'), array('', '-', '_', '/', ''), $str));
    }
}

