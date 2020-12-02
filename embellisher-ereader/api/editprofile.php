<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_POST["email"] );

$interests = $DB->escape_string( $_POST["interests"] );
$genre = $DB->escape_string( $_POST["genre"] );
$public_private = $DB->escape_string( $_POST["public_private"] );
$newpassword =  $_POST["newpassword"] ;

$pass = "";
if ($newpassword != "" && strlen($newpassword) > 3){
	$pass = $DB->real_escape_string(md5($newpassword,$salt));
}elseif ($newpassword != ""){
	$result['msg'] = "New password should be at least 4 characters long!";
	echo json_encode($result);
	exit();
}



$result = array();
$SQL = "SELECT * FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	$UPDATESQL = "UPDATE user SET interests='$interests', genre_of_writing='$genre', public_private='$public_private'  WHERE email='$username'";
	
	if ($pass != ""){
		$UPDATESQL = "UPDATE user SET interests='$interests', genre_of_writing='$genre', public_private='$public_private', password='$pass'  WHERE email='$username'";
	}
	$DB->query($UPDATESQL);
	$result['msg'] = "Preferences updated!";
}else{
	$result['msg'] = "Session does not exist";
}
echo json_encode($result);