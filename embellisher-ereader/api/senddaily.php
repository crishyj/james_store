<?php


require('config.php');


if (!isset($_GET["name"]) || $_GET["name"]!="SG"){
	exit();
}

$GET_TEMPLATES = "SELECT * FROM emailtemplates WHERE day>0 ORDER BY day DESC";
$TEMPLATES = $DB->query($GET_TEMPLATES);

while ($EMAIL = $TEMPLATES->fetch_assoc()){
	$day = $EMAIL['day'];
	$nextday = $day+1;

	$GET_USERS = "SELECT * FROM user WHERE subscribed='$day'";
	$USERS = $DB->query($GET_USERS);
	while ($user = $USERS->fetch_assoc()){
		$username = $user['email'];
		$name = $user['name'];
		$to = $username;

		$subject = $EMAIL['name'];
		$email_from = "noreply@emrepublishing.com";
		$htmlemail = $EMAIL['content'];
		$htmlemail = str_replace("##EMAIL_PLACEHOLDER##", $username, $htmlemail);
		$htmlemail = str_replace("##NAME_PLACEHOLDER##", $name, $htmlemail);
		$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n" .'X-Mailer: PHP/' . phpversion()."\r\nContent-type: text/html\r\n";
		@mail($to, $subject, $htmlemail, $headers);
	}


	$UPDATE_SUB = "UPDATE user SET subscribed='$nextday' WHERE subscribed ='$day'";
	$DB->query($UPDATE_SUB);

}


