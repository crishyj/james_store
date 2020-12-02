<?php 
include 'config.php';
ob_start();
session_start();

//RESET ALL COOKIES :) - I LIKE COOKIES
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}
session_destroy();

            //echo 'succes';
            echo '
            <html>
            <head>
            <link rel="stylesheet"  media="all" href="../css/bootstrap.css" />
            <META HTTP-EQUIV="refresh" CONTENT="1;URL=index.php">
            
            
            <title>Uitloggen</title>
            </head>
            <body>
            <div style="margin-top:10%;">
            
            <div class="center">
            <center> 
            <img src="images/about-embellisher-logo.png"></img><br/><br/>
            <h2>You are logged out, please wait..</h2>
            
          
            </center>
            </div>
            
            
            </div>
            </body>
            </html>';

ob_end_flush();

?>

