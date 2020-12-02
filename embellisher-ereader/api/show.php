<?php

require('config.php');
$session = $DB->escape_string( $_REQUEST["sessionid"] );
$username = $DB->escape_string( $_REQUEST["email"] );


if (!isset($_REQUEST['filename']) || !isset($_REQUEST['sessionid'])){
	echo "Session expired, login and try again.";
	exit();
}
$filename = $_REQUEST['filename'];


//$SQL = "SELECT user.id as userid FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
//$RES = $DB->query($SQL);
//if ($user = $RES->fetch_assoc()){
	//$name = $filename;
	//$mime = mime_content_type($filename);
	//echo $mime;
	//$fp = fopen($name, 'r');
	
	//header("Content-Type: ".$mime);
	//finfo_close($finfo);
	//header("Content-Length: " . filesize($name));
	//fpassthru($fp);
	$file = file_get_contents($filename);
	echo $file;
	//include $filename;
	//exit;
//}else{
//	echo "Session expired, login and try again.";
//}

