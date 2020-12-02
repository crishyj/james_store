<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$file = dirname(dirname(__file__));
require_once dirname(__FILE__)."/src/phpfreechat.class.php";


$params = array();
$params["title"] = "Embellischer Chat";

$name = "g".rand(0,1000);
if (isset($_GET['name']) && $_GET['name']!=""){
  $name = $_GET['name'];
}

$name =  substr($name,0,strpos($name,' '));

$params['admins'] = array('bas'  => 'Bush0kje');

$params["nick"] = $name;  // setup the intitial nickname
$params["theme"] = "sg";
$params['firstisadmin'] = false;
//$params["isadmin"] = False; // makes everybody admin: do not use it on production servers ;)
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
$params["channels"] = array("General");
$params["debug"] = false;
$params["height"] = "250px";
$params["showwhosonline"] = false;
$params["shownotice"]     = 0; 
$params["clock"]     = false; 
$chat = new phpFreeChat( $params );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Embellischer chat</title>
  <link rel="stylesheet" title="classic" type="text/css" href="style/generic.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/content.css" />  
 </head>
 <body>



<div class="content">
  <?php $chat->printChat(); ?>

</div>

    
</body></html>
<?php
/*
require_once dirname(__FILE__)."/src/phpfreechat.class.php";
$params = array();
$params["title"] = "Quick chat";
$params["nick"] = "guest".rand(1,1000);  // setup the intitial nickname
$params['firstisadmin'] = true;
//$params["isadmin"] = true; // makes everybody admin: do not use it on production servers ;)
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
$params["debug"] = false;
$chat = new phpFreeChat( $params );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>phpFreeChat- Sources Index</title>
  <link rel="stylesheet" title="classic" type="text/css" href="style/generic.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/header.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/footer.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/menu.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/content.css" />  
 </head>
 <body>

<div class="header">
      <img alt="phpFreeChat" src="style/logo.gif" class="logo2" />
</div>

<div class="menu">
      <ul>
        <li class="sub title">General</li>
        <li>
          <ul class="sub">
            <li class="item">
              <a href="demo/">Demos</a>
            </li>
            <?php if (file_exists(dirname(__FILE__)."/checkmd5.php")) { ?>
            <li>
              <a href="checkmd5.php">Check md5</a>
            </li>
            <?php } ?>
            <!--
            <li class="item">
              <a href="admin/">Administration</a>
            </li>
            -->
          </ul>
        </li>
        <li class="sub title">Documentation</li>
        <li>
          <ul>
            <li class="item">
              <a href="http://www.phpfreechat.net/overview">Overview</a>
            </li>
            <li class="item">
              <a href="http://www.phpfreechat.net/quickstart">Quickstart</a>
            </li>
            <li class="item">
              <a href="http://www.phpfreechat.net/parameters">Parameters list</a>
            </li>
            <li class="item">
              <a href="http://www.phpfreechat.net/faq">FAQ</a>
            </li>
            <li class="item">
              <a href="http://www.phpfreechat.net/advanced-configuration">Advanced configuration</a>
            </li>
            <li class="item">
              <a href="http://www.phpfreechat.net/customize">Customize</a>
            </li>
          </ul>
        </li>
      </ul>
      <p class="partner">
        <a href="http://www.phpfreechat.net"><img alt="phpfreechat.net" src="style/logo_88x31.gif" /></a><br/>
      </p>
</div>

<div class="content">
  <?php $chat->printChat(); ?>
  <?php if (isset($params["isadmin"]) && $params["isadmin"]) { ?>
    <p style="color:red;font-weight:bold;">Warning: because of "isadmin" parameter, everybody is admin. Please modify this script before using it on production servers !</p>
  <?php } ?>
</div>

<div class="footer">
  <span class="partners">-</span>
</div>
    
</body></html>*/
?>
