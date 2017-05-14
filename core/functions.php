<?php
/* 
 * Created by Chris Dekker.
 * 
 * Date: 13-1-2016
 * 
 * Functions File
 */

function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function generateRandomString($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}