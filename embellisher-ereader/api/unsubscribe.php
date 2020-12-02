<?php


require('config.php');


if (!isset($_GET["email"])){
	exit();
}else{
	$email = $DB->real_escape_string($_GET['email']);
	$sql = "UPDATE user SET subscribed=0 WHERE email='$email'";
	$DB->query($sql);
	echo "You are now unsubscribed. Thank you for using the Embellisher Ereader and ePub Creator Studio.";
}