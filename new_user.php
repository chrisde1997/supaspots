<?php
/**
 * Created by PhpStorm.
 * User: Social Brothers
 * Date: 14-5-2017
 * Time: 07:56
 */

require_once 'core/init.php';

if(isset($_GET['id'])) {

    $user_id = escape($_GET['id']);

    $u = new User();

    if($u->newUser($user_id)) {

        Cookie::put('user_id', $user_id, time() + 3600, '/supaspots/');

        Redirect::to('/supaspots/select_playlist');

    } else {

        require_once VIEW_ROOT . '/general/error_view.php';

    }

} else {

    Redirect::to('/supaspots/');

}