<?php
/* 
 * Created by Chris Dekker.
 * 
 * Date: 13-1-2016
 * 
 * Config Class
 */

class Config {
    public static function get($path = null) {
        if($path) {
            $config = $GLOBALS['config'];
            $path = explode('/', $path);
            
            foreach($path as $bit) {
                if(isset($config[$bit])) {
                    $config = $config[$bit];
                }
            }
            
            return $config;
        }
        
        return false;
    }
}