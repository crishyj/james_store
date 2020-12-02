<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_POST["email"] );
$bookid = $DB->escape_string( $_POST["bookid"] );

$result = array();

$SQL = "SELECT user.id as userid FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	
	$userid = $user['userid'];
	$deleteSQL = "DELETE FROM user_library WHERE userid='$userid' AND libraryid='$bookid'";
	$DB->query($deleteSQL);
	$result['msg'] = "success";
}else{
	$result['error'] = "LOGIN";
}
echo json_encode($result);