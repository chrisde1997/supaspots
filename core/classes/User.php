<?php
/* 
 * Created by Chris Dekker.
 * 
 * Date: 13-1-2016
 * 
 * User Class
 */

class User {

    private $_db;
    
    public function __construct() {

        $this->_db = DB::getInstance();

    }

    function authorize($code) {

        $client_id      = "61e9aae2c9434469b3c3690bc82c0958";
        $client_secret  = "9260acdaa1724ef0a8587907b9cd6265";
        $client         = base64_encode($client_id . ":" . $client_secret);
        $redirect       = "http://localhost/supaspots/callback";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://accounts.spotify.com/api/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirect
        )));

        $headers = [
            'Authorization: Basic ' . $client
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);

        curl_close ($ch);

        $json = json_decode($resp);
        Cookie::put('access_token', $json->access_token, time() + 3600, '/supaspots/');

        echo $resp;
        exit;
    }

    function getCurrentUserPlaylists() {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/me/playlists");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($ch);

        curl_close ($ch);

        echo $resp;
        exit;

    }

    function selectPlaylist($playlist, $owner) {

        if($this->_db->update('playlists', Cookie::get('user_id'), array(

            'spotify_id' => $playlist

        ), 'spotify_user')) {

            Cookie::put('playlist', $playlist, time() + 3600, '/supaspots/');
            Cookie::put('playlist_owner', $owner, time() + 3600, '/supaspots/');

            echo json_encode(array('status' => true, 'playlist' => $playlist));
            exit;

        } else {

            echo json_encode(array('status' => false));
            exit;

        }

    }

    function newUser($user_id) {

        if($this->_db->insert('playlists', array(

            'spotify_user' => $user_id

        ))) {

            return true;

        } else {

            return false;

        }

    }

    function userExists() {

        $user = json_decode($this->getUser());

        $this->_db->get('playlists', array('spotify_user', '=', $user->id));

        if(!empty($this->_db->results())) {

            echo json_encode(array('status' => true, 'user_id' => $user->id));
            exit;

        } else {

            echo json_encode(array('status' => false, 'user_id' => $user->id));
            exit;

        }

    }

    function storePlaylistData($user_id, $playlist_id) {

        date_default_timezone_set('Europe/Amsterdam');

        $this->_db->insert('playlists', array(

            'spotify_id'    => $playlist_id,
            'spotify_user'  => $user_id,
            'datetime'      => date('Y-m-d h:i:s', time())

        ));

    }

    function findUser($user_id) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/users/" . $user_id);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($ch);

        curl_close ($ch);

        echo $resp;
        exit;

    }

    function addSong($song) {

        $user_id        = Cookie::get('user_id');
        $playlist_id    = Cookie::get('playlist');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/users/" . $user_id . "/playlists/" . $playlist_id . "/tracks");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            'uris' => array($song)
        )));

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token'),
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);

        curl_close ($ch);

        echo $resp;
        exit;

    }

    function getUserData() {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/me");

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);

        curl_close ($ch);

        echo $resp;
        exit;

    }

    private function getUser() {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/me");

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);

        curl_close ($ch);

        return $resp;

    }

    function search($query) {

        $query = str_replace(' ', '%20', $query);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/search?q=" . $query . "&type=track&limit=1");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($ch);

        curl_close ($ch);

        echo $resp;
        exit;

    }

    function getPlaylist() {

        $user_id        = Cookie::get('playlist_owner');
        $playlist_id    = Cookie::get('playlist');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/users/" . $user_id . "/playlists/" . $playlist_id . "/tracks");

        $headers = [
            'Authorization: Bearer ' . Cookie::get('access_token')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);

        curl_close ($ch);

        echo $resp;
        exit;

    }

    function logout() {

        Cookie::delete('access_token', time() - 3600, '/supaspots/');
        Cookie::delete('user_id', time() - 3600, '/supaspots/');
        Cookie::delete('playlist', time() - 3600, '/supaspots/');

        echo true;
        exit;

    }
}