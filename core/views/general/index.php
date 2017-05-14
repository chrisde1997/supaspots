<?php require_once VIEW_ROOT . '/templates/header.php'; ?>

<script>

    $(document).ready(function() {

        if (!!$.cookie('access_token')) {

            $.ajax({

                url: '/supaspots/inc/magic.php',
                type: 'POST',
                data: {
                    action: 'user_exists'
                },
                success: function (data) {

                    var $json = $.parseJSON(data);

                    if($json.status === true) {

                        if(!!$.cookie('user_id')) {

                            if(!!$.cookie('playlist')) {

                                getUserData();

                            } else {

                                window.open('http://localhost/supaspots/select_playlist', '_self');

                            }

                        } else {

                            window.open('http://localhost/supaspots/select_playlist?id=' + $json.user_id, '_self');

                        }

                    } else {

                        window.open('http://localhost/supaspots/new_user?id=' + $json.user_id, '_self');

                    }

                },
                error: function (request, error) {
                    console.log(error);
                }
            });

        } else {

        }

        getPlaylist();

        $('#log').DataTable({
            "searching": false
        });

        //setup before functions
        var typingTimer;
        var doneTypingInterval = 2000;
        var $input = $('#search');

        //on keyup, start the countdown
        $input.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        //on keydown, clear the countdown
        $input.on('keydown', function () {
            clearTimeout(typingTimer);
        });

        $('#add_button').prop('disabled', true);

        //user is "finished typing," do something
        function doneTyping () {

            $.ajax({

                url : '/supaspots/inc/magic.php',
                type : 'POST',
                data : {
                    action: 'search',
                    query: $('#search').val()
                },
                success : function(data) {
                    var $json = $.parseJSON(data);

                    var $artists;

                    if(Object.keys($json.tracks.items[0].artists).length > 1) {

                        var array = $.map($json.tracks.items[0].artists, function(value, index) {
                            return [value.name];
                        });

                        $artists = array.join(", ");
                        console.log(array);

                    } else {

                        $artists = $json.tracks.items[0].artists[0].name;

                    }

                    $('.search_output').html("<p>Titel: " + $json.tracks.items[0].name + "</p><p>Artiest(en): " + $artists + "</p>");

                    $('#add_button').prop('disabled', false);

                    $('#add_button').click(function () {

                        addSong($json.tracks.items[0].uri);

                    });
                },
                error : function(request,error)
                {
                    console.log(error);
                }
            });

        }

    });

    function addSong($uri) {

        $.ajax({

            url: '/supaspots/inc/magic.php',
            type: 'POST',
            data: {
                action: 'add_song',
                song: $uri
            },
            success: function (data) {

                window.location.reload();

            },
            error: function (request, error) {
                console.log(error);
            }
        });

    }

    function getPlaylist() {

        $('#playlist').hide();
        $('.playlist-alert').hide();
        $('body').addClass("loading");

        if ($.cookie('access_token')) {

            $.ajax({

                url: '/supaspots/inc/magic.php',
                type: 'POST',
                data: {
                    action: 'get_playlist'
                },
                success: function (data) {

                    console.log(data);

                    var $json = $.parseJSON(data);

                    $.each($json.items, function (key, value) {

                        $.ajax({

                            url: '/supaspots/inc/magic.php',
                            type: 'POST',
                            data: {
                                action: 'find_user',
                                user_id: value.added_by.id
                            },
                            success: function (data) {

                                $user = $.parseJSON(data);

                                var $display_name;

                                if($user.display_name === null) {

                                    $display_name = $user.id;

                                } else {

                                    $display_name = $user.display_name;

                                }

                                $('#playlist_inner').append("<tr><td>" + value.track.name + "</td><td>" + $display_name + "</td></tr>");

                            },
                            error: function (request, error) {
                                console.log(error);
                            }
                        });

                    });

                    setTimeout(initializeDataTable, $json.items.length * 1000);

                },
                error: function (request, error) {
                    console.log(error);
                }
            });

        } else {

            $('body').removeClass("loading");

        }

    }

    function initializeDataTable() {
        $('#playlist').DataTable();
        $('#playlist').show();
        $('body').removeClass("loading");
    }

    function spotifyLogin() {

        var $client_id  = "61e9aae2c9434469b3c3690bc82c0958";
        var $redirect   = "http://localhost/supaspots/callback";
        var $scope      = "user-read-private playlist-modify-public playlist-read-collaborative playlist-modify-private";

        window.location.href = 'https://accounts.spotify.com/authorize?client_id=' + $client_id + '&scope=' + $scope + '&response_type=code&redirect_uri=' + $redirect;

    }

    function getUserData() {

        $.ajax({

            url : '/supaspots/inc/magic.php',
            type : 'POST',
            data : {
                action: 'user_data'
            },
            success : function(data) {
                var $json = $.parseJSON(data);

                $('#user_name').html($json.display_name);
                $('#user_name_header').html($json.display_name);
                $('#user_image').html('<img src="' + $json.images[0].url + '" />');

            },
            error : function(request,error)
            {
                console.log(error);
            }
        });

    }

    function logout() {

        $.ajax({

            url : '/supaspots/inc/magic.php',
            type : 'POST',
            data : {
                action: 'logout'
            },
            success : function(data) {
                window.location.reload();
            },
            error : function(request,error)
            {
                console.log(error);
            }
        });

    }

</script>

<?php if(!Cookie::exists('access_token')): ?>

    <section class="top">
        <div class="container">

            <div class="row">

                <div class="col-md-12">
                    <h4>Welkom bij de SupaSpots</h4>
                    <p>Log in met Spotify om te beginnen!</p>
                    <?php if(Cookie::exists('access_token')): ?>
                        <p>Cookie exists though</p>
                    <?php endif; ?>
                    <?php print_r($_COOKIE); ?>
                </div>

            </div>

        </div>
    </section>

<?php else: ?>

    <section class="top">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="col-md-6">
                        <div id="user_image"></div>
                    </div>
                    <div class="col-md-6">
                        <h4>Je bent ingelogd! Dankjewel!</h4>
                        <p>Welkom <span id="user_name"></span></p>
                    </div>
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <div class="col-md-7">
                    <h4>Log</h4>
                    <table id="log" class="stripe">
                        <thead>
                        <tr>
                            <th>Actie</th>
                        </tr>
                        </thead>
                        <tbody id="log_inner">

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Adding Songs -->
            <div class="row">
                <div class="col-md-5 col-md-offset-4">
                    <h4>Een nummer toevoegen</h4>
                    <input type="search" class="form-control" id="search" placeholder="Titel van het nummer">
                    <br />
                    <pre class="search_output">
                        <p>Zoek een nummer door hierboven een titel in te voeren</p>
                    </pre>
                    <br />
                    <button class="btn btn-default" id="add_button">Toevoegen</button>
                </div>
            </div>
            <!-- The Playlist -->
            <div class="row">
                <div class="col-md-12">
                    <h4>De afspeellijst</h4>
                    <br/>
                    <div class="alert alert-info">
                        <strong>Hé waar is mijn nummer?</strong> Het kan even duren voordat je nummer hier te zien is, maar hij staat al wel in de playlist hoor!
                    </div>
                    <br/>
                    <table id="playlist" class="stripe">
                        <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Toegevoegd door</th>
                        </tr>
                        </thead>
                        <tbody id="playlist_inner">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>
