<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$password = $DB->escape_string( $_POST["password"] );
$username = $DB->escape_string( $_POST["email"] );

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$result = array();
$SQL = "SELECT * FROM user WHERE email='$username'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	// Deze functie zal de naam valideren
    function validate_name($username)
    {
        $u_length = strlen($username);
        if ($u_length <= "3" OR $username == "")
        {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    // Deze functie zal kijken of de inlog gegevens kloppen
    function validate_login($username,$password,$password2,$salt)
    {
		$password = md5($password);
		if ($password2 != $password)
		{
			return FALSE;
		} else {
			return TRUE;
		}
    }
    
    if (!validate_name($username))
    {
        $result['error'] = "Incorrect password";
		$result['loggedin'] = 0;
    } elseif (!validate_login($username, $password, $user['password'], $salt)) {
        $result['error'] = "Incorrect password";
		$result['loggedin'] = 0;
    } else{
		$result = $user; //success!
		$result['loggedin'] = 1;

        $userid = $user['id'];
		
		$sessionid = $DB->real_escape_string(generateRandomString(10));
		$result['sessionid'] = $sessionid;
		//$UPDATESQL = "UPDATE user SET sessionid='$sessionid' WHERE email='$username'";
        $INSERTSESSION = "INSERT INTO sessions (userid, sessionid) VALUES ('$userid','$sessionid')";

		$DB->query($INSERTSESSION);
        $countres = $DB->query("SELECT COUNT(*) as c FROM sessions WHERE userid='$userid'");
        $count = $countres->fetch_assoc();
        if ($count['c'] > $user['maxsessions']){
            //kill the first active session.
            $SELECTLASTSESSION = "SELECT id FROM sessions WHERE userid='$userid' ORDER BY id ASC LIMIT 1";
            $resid = $DB->query($SELECTLASTSESSION)->fetch_assoc();
            $DELETESESSION = "DELETE FROM sessions WHERE id = ".$resid['id'];
            $DB->query($DELETESESSION);
        }
        //Check max sessions
	}
}else{
	$result['loggedin'] = 0;
	$result['error'] = "Email is not valid or session is expired.";
}
echo json_encode($result);