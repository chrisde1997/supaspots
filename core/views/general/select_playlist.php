<?php require_once VIEW_ROOT . '/templates/header.php'; ?>

<script>

    $(document).ready(function() {

        $('body').addClass("loading");
        $('.playlist-selected').hide();
        $('#playlist_continue').prop('disabled', true);

        $.ajax({

            url: '/supaspots/inc/magic.php',
            type: 'POST',
            data: {
                action: 'current_user_playlists'
            },
            success: function (data) {

                console.log(data);

                var $json = $.parseJSON(data);

                var $playlists = [];

                $.each($json.items, function (key, value) {

                    if (value.collaborative === true) {

                        $playlists.push(value);

                    }

                });

                $.each($playlists, function (key, value) {

                    $('#playlists').append('<li><i class="fa fa-arrow-right" aria-hidden="true"></i> <a href="#" id="' + value.id + '" onclick="selectPlaylist(\'' + value.id + '\', \'' + value.owner.id + '\')">' + value.name + '</a></li>');

                });

                $('body').removeClass("loading");

            },
            error: function (request, error) {
                console.log(error);
            }
        });

    });

    function selectPlaylist($playlist_id, $owner) {

        var $playlist_name = $('#' + $playlist_id).html();

        $.ajax({

            url: '/supaspots/inc/magic.php',
            type: 'POST',
            data: {
                action: 'select_playlist',
                playlist: $playlist_id,
                owner: $owner
            },
            success: function (data) {

                $('.playlist-selected').html('<p>Je hebt ' + $playlist_name + ' geselecteerd!</p>').show();
                $('#playlist_continue').prop('disabled', false);

            },
            error: function (request, error) {
                console.log(error);
            }
        });

    }

    function continuePlaylist() {

        window.open('http://localhost/supaspots/', '_self');

    }

</script>

<section class="top">
    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h4>Kies een playlist die je wilt gebruiken:</h4>

                <div class="alert alert-info">
                    <strong>Welkom!</strong> Je ziet hier je "gezamelijke" afspeellijsten. Zie je hier niets? Dan heb je waarschijnlijk geen afspeellijsten die je kunt gebruiken. <a href="/supaspots/faq#no-playlist"><strong>Wat nu?</strong></a>
                </div>

                <ul id="playlists">


                </ul>

                <div class="alert alert-success playlist-selected">

                </div>

                <button class="btn btn-default" id="playlist_continue" onclick="continuePlaylist()"><i class="fa fa-hand-o-right" aria-hidden="true"></i> Doorgaan</button>

            </div>

        </div>

    </div>
</section>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>
