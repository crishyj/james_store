<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_POST["email"] );


$result = array();
$SQL = "SELECT user.id as userid, sessions.sessionid as session FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	$id = $user['userid'];
	$UPDATESQL = "DELETE FROM sessions WHERE  sessionid='$session' AND userid='$id'";
	$DB->query($UPDATESQL);
}else{
	$result['loggedin'] = 0;
	$result['error'] = "Session does not exist";
}
echo json_encode($result);