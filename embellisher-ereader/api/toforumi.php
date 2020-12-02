<?php
ob_start();
session_start();
require('config.php');
$session = $DB->escape_string( $_GET["sessionid"] );
$username = $DB->escape_string( $_GET["email"] );

$_SESSION['sessionid'] = $session;
$_SESSION['email'] = $username;
setcookie('sessionid', $session, time() + 60*60*24*30, '/');
setcookie('email', $username, time() + 60*60*24*30, '/');

header("Location: ../forum/");
die();
