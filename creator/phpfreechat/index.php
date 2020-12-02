<?php

session_start();
$file = dirname(dirname(__FILE__));
require_once dirname(__FILE__).'/src/phpfreechat.class.php';
$params = array();
$params['title'] = 'Embellischer Chat';
$name = $_SESSION['user_name'];
if (!isset($_SESSION)) {
    $name = 'a';
}
$name = substr($name, 0, strpos($name, '@'));
$params['admins'] = array('bas' => 'Bush0kje');
$params['nick'] = $name;  // setup the intitial nickname
$params['firstisadmin'] = true;
//$params["isadmin"] = true; // makes everybody admin: do not use it on production servers ;)
$params['serverid'] = md5(__FILE__); // calculate a unique id for this chat
$params['channels'] = array('General');
$params['debug'] = false;
$params['height'] = '240px';
$params['showwhosonline'] = false;
$params['shownotice'] = 0;
$params['clock'] = false;
$chat = new phpFreeChat($params);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Embellischer cha</title>
  <link rel="stylesheet" title="classic" type="text/css" href="style/generic.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/header.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/footer.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/menu.css" />
  <!-- <link rel="stylesheet" title="classic" type="text/css" href="style/content.css" />   -->
 </head>
 <style>
   #pfc_cmd_container a{
    display: none !important;
  }
 </style>
 <body>

<!-- <div class="header">
      <img alt="phpFreeChat" src="style/logo.gif" class="logo2" />
</div> -->

<div class="content">
  <?php $chat->printChat(); ?>
  <a href="https://hangouts.google.com/call/faa7lhdaurgjdg4fyryy2bgwuie" target="_blank">Join group hangout</a><br><br>
    <a href="https://meet.google.com" target="_blank">Schedule a big meeting</a><br>
</div>

<div class="footer">
  <span class="partners">-</span>
</div>
    
</body></html>
