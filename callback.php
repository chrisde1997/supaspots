<?php
/**
 * Created by PhpStorm.
 * User: Social Brothers
 * Date: 13-5-2017
 * Time: 11:54
 */

require_once 'core/init.php';

require_once VIEW_ROOT . '/templates/header.php';

if(isset($_GET['error'])):

    require_once VIEW_ROOT . '/general/error_view.php';

elseif(isset($_GET['code'])):

?>

    <section class="top">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>Authorized!</h4>
                    <p>Doing the magic...</p>
                </div>
                <div class="col-md-12">
                    <h4>Momentje..</h4>
                    <p>Je word doorgestuurd over <span id="counter"></span> seconden.</p>
                </div>
            </div>
        </div>
    </section>

    <script>

        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        $.ajax({

            url : '/supaspots/inc/magic.php',
            type : 'POST',
            data : {
                code: getParameterByName('code'),
                action: 'authorize'
            },
            success : function(data) {
                var count = 5;
                var countdown = setInterval(function(){
                    $("#counter").html(count);
                    if (count == 0) {
                        clearInterval(countdown);
                        window.open('http://localhost/supaspots/', "_self");

                    }
                    count--;
                }, 1000);
            },
            error : function(request,error)
            {
                console.log(error);
            }
        });

    </script>

<?php else: ?>



<?php endif; ?>

<?php require_once VIEW_ROOT . '/templates/footer.php'; ?>
