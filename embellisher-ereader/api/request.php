<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$session = $DB->escape_string( $_GET["sessionid"] );
$username = $DB->escape_string( $_GET["email"] );
$file = $DB->escape_string( $_GET["file"] );

$result = array();

$freebooks = array("epub_content/welcome_to_english_majors_reviewers_and_editors","epub_content/steam_city_pirates-jim_musgrave-sample","epub_content/forevermore-jim_musgrave","epub_content/running_with_the_big_dogs-jim_musgrave");

if (in_array($file,$freebooks) || strpos($file,'creator') !== false){
	$result['msg']="success";
	echo json_encode($result);
	exit();
}

$SQL = "SELECT user.id as userid FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	//TODO: get real result from database
	$userid = $user['userid'];
	$SELECTBOOK = "SELECT * FROM library, user_library WHERE library.rootUrl = '$file' AND library.id = user_library.libraryid and user_library.userid='$userid'";
	$RES = $DB->query($SELECTBOOK);
	if ($book = $RES->fetch_assoc()){
		$result['msg']="success";
	}else{
		$result['msg']="You did not buy this book.";
	}
	
	

}else{
	$result['msg']="Session expired";
}
echo json_encode($result);

