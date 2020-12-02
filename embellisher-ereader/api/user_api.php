<?php


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function get_user_function($session,$email){
	include ('config.php');
	$result = array();
	$SQL = "SELECT user.id as id, user.email as email, user.name as name, user.interests as interests, user.admin as admin FROM user,sessions WHERE email='$email' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
	$RES = $DB->query($SQL);
	if ($user = $RES->fetch_assoc()){
		$result = $user;
	}else{
		//empty
	}
	return $result;

}
