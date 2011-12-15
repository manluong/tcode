<!DOCTYPE html><html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>#title#</title>
<!-- <script type="text/javascript" src="https://getfirebug.com/firebug-lite-debug.js"></script> -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

<!-- from head START-->


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

<?php
	if (isset($success_msg) && $success_msg!='') echo '<p>Success: ',$success_msg,'</p>';
	if (isset($error_msg) && $error_msg!='') echo '<p>Error: ',$error_msg,'</p>';
?>