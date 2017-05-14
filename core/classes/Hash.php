<?php
/* 
 * Created by Chris Dekker.
 * 
 * Date: 13-1-2016
 * 
 * Hash Class
 */

class Hash {
    public static function make($string, $salt = '') {
        return hash('sha256', $string . $salt);
    }
    
    public static function salt($length) {
        return mcrypt_create_iv($length);
    }
    
    public static function unique() {
        return self::make(uniqid());
    }
}