<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_POST["email"] );


$result = array();

$SQL = "SELECT user.id as userid FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	//TODO: get real result from database
	$userid = $user['userid'];
	$selectSQL = "SELECT * FROM library WHERE id IN (SELECT libraryid FROM user_library WHERE userid='$userid')";
	$resbook = $DB->query($selectSQL);
	while ($library_item = $resbook->fetch_assoc() ){
		$result[] = $library_item;
	}

}else{
	$result['error'] = "LOGIN";
}
echo json_encode($result);