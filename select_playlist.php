<?php
/**
 * Created by PhpStorm.
 * User: Social Brothers
 * Date: 14-5-2017
 * Time: 07:59
 */

require_once 'core/init.php';

if(isset($_GET['id'])) {

    Cookie::put('user_id', escape($_GET['id']), time() + 3600, '/supaspots/');

}

require_once VIEW_ROOT . '/general/select_playlist.php';