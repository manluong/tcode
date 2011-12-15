<!DOCTYPE html><html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title><?=$title?></title>
<!-- <script type="text/javascript" src="https://getfirebug.com/firebug-lite-debug.js"></script> -->

<!-- from head START-->

<?=$head?>

<!-- from head END -->

<!-- static -->

<script>

$(window).load(function(){

    $("#loading").fadeOut(function(){

        $(this).remove();

        $('body').removeAttr('style');

    });

    //notification_autoclose('notification_spot');
});

</script>

<style type = "text/css">

    #container {position: absolute; top:50%; left:50%;}

    #content {width: 800px; text-align:center; margin-left: -400px; height:50px; margin-top:-25px; line-height: 50px;}

    #content {font-family: "Helvetica", "Arial", sans-serif; font-size: 18px; color: black; text-shadow: 0px 1px 0px white; }

    #loadinggraphic {margin-right: 0.2em; margin-bottom:-2px;}

    #loading {background-color: #eeeeee; overflow:hidden; width:100%; height:100%; position: absolute; top: 0; left: 0; z-index: 9999;}

</style>

<!-- static END -->

</head>

<body style="overflow: hidden;">

    <div id="loading">

        <script type = "text/javascript">

            document.write("<div id='container'><p id='content'>" +

                           "<img id='loadinggraphic' width='16' height='16' src='html/layout/tpanel/images/ajax-loader-eeeeee.gif' /> " +

                           "Loading...</p></div>");

        </script>

    </div>



    <div id="wrapper" class="clearfix">

            <?=$top?>

        <section>

            <div class="container_main clearfix">

                <section class="main-section full">

                <?=$menumain?>

                <div class="main-content">

                <?=$menuapp?>

                        <section class="container_6 clearfix">

                            <div id="xmsg"></div>
                            <!-- app content START -->
                            <?=$content?>
                            <!-- app content END -->

                        </section>
                    </div>

                </section>

            </div>

        </section>

    </div>

    <footer>

        <div id="footer-inner">

                <span class="fr">&copy; 2011. All rights reserved. Telcoson Pte Ltd | <a href="#">Feedback</a></span>

        </div>

    </footer>

</body>

</html>