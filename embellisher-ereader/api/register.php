<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');

require('config.php');

// if (PHPLIST != "")
// 	require('phplist.php');

$result = array();

if (!isset($_POST["password"]) || strlen($_POST["password"]) < 4){
	$result['msg'] = "Please fill in a password with a minimum size of 4.";
	echo json_encode($result);
	exit();
}
if (!isset($_POST["username"])){
	$result['msg'] = "Please fill in your email.";
	echo json_encode($result);
	exit();
}
if (!isset($_POST["name"])){
	$result['msg'] = "Please fill in your name.";
	echo json_encode($result);
	exit();
}
$type = "Reader";
if (isset($_POST["type"])){
	$type = $DB->real_escape_string( $_POST["type"] );
}
$storeid = "0";
if (isset($_POST["storeid"]) && SEPARATE_ADMINS){
	$storeid = $DB->real_escape_string( $_POST["storeid"] );
}
$password = $_POST["password"] ;
$username = $DB->real_escape_string( $_POST["username"] );
$name = $DB->real_escape_string($_POST["name"] );

$SQL = "SELECT * FROM user WHERE email='$username'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	$result['msg'] = "User already exists.";
}else{
	$pass = $DB->real_escape_string(md5($password));
	$SQL = "INSERT INTO user (name,type,email,password,storeid) VALUES ('$name','$type', '$username','$pass','$storeid')";
	$RES = $DB->query($SQL);
	$userid = $DB->insert_id;
	$GETBOOKS = "SELECT id FROM library WHERE preload=1";
	$BOOKS = $DB->query($GETBOOKS);
	
	//Free epubs!
	while ($book = $BOOKS->fetch_assoc()){
		$bookid = $book['id'];
		$SQL = "INSERT INTO user_library (userid,libraryid) VALUES ('$userid','$bookid')";
		$RES = $DB->query($SQL);
	}
	
	$result['msg']="success";

	$GET_WELCOME_EMAIL = "SELECT * FROM emailtemplates WHERE id=2";
	if ($R = $DB->query($GET_WELCOME_EMAIL)){
		if ($WELCOME_EMAIL = $R->fetch_assoc()){
			$to = $username;
			$subject = $WELCOME_EMAIL['name'];
			$email_from = "noreply@emrepublishing.com";
			$htmlemail = $WELCOME_EMAIL['content'];
			$htmlemail = str_replace("##EMAIL_PLACEHOLDER##", $username, $htmlemail);
			$htmlemail = str_replace("##NAME_PLACEHOLDER##", $name, $htmlemail);
			$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n" .'X-Mailer: PHP/' . phpversion()."\r\nContent-type: text/html\r\n";
			@mail($to, $subject, $htmlemail, $headers);
		}
	}

// 	//subscribe user to phplist
// 	if (PHPLIST != ""){
// 		$list = new phpList('/lists/');
// 		$userId = $list->createUser($username,array('name'=>$name));
// 		$list->subscribe($userId,2);
// 		$list->subscribe($userId,3);
// 	}
// 	//$result['SQL']=$SQL;
}

echo json_encode($result);
?>