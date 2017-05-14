<?php
/* 
 * Created by Chris Dekker.
 * 
 * Date: 13-1-2016
 * 
 * Cookie Class
 */

class Cookie {
    public static function exists($name) {
        return (isset($_COOKIE[$name])) ? true : false;
    }
    
    public static function get($name) {
        return $_COOKIE[$name];
    }
    
    public static function put($name, $value, $expiry, $path) {
        if(setcookie($name, $value, $expiry, $path)) {
            return true;
        }
        
        return false;
    }
    
    public static function delete($name, $expiry, $path) {
        self::put($name, '', $expiry, $path);
    }
}