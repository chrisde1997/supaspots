<?php
/**
 * Created by PhpStorm.
 * User: Social Brothers
 * Date: 13-5-2017
 * Time: 11:34
 */

require_once '../core/init.php';

if(isset($_POST['action'])) {

    $user = new User();

    switch($_POST['action']) {

        case 'authorize':

            $user->authorize($_POST['code']);

            break;

        case 'user_data':

            $user->getUserData();

            break;

        case 'get_playlist':

            $user->getPlaylist();

            break;

        case 'select_playlist':

            $user->selectPlaylist($_POST['playlist'], $_POST['owner']);

            break;

        case 'user_exists':

            $user->userExists();

            break;

        case 'current_user_playlists':

            $user->getCurrentUserPlaylists();

            break;

        case 'find_user':

            $user->findUser($_POST['user_id']);

            break;

        case 'search':

            $user->search($_POST['query']);

            break;

        case 'add_song':

            $user->addSong($_POST['song']);

            break;

        case 'logout':

            $user->logout();

            break;


        default:


    }
}