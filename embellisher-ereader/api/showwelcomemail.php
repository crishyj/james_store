<?php


require('config.php');


if (!isset($_GET["name"])){
	exit();
}

$name = htmlspecialchars($_GET["name"]);
$GET_WELCOME_EMAIL = "SELECT * FROM emailtemplates WHERE id=2";
$WELCOME_EMAIL = $DB->query($GET_WELCOME_EMAIL)->fetch_assoc();
$htmlemail = $WELCOME_EMAIL['content'];
$htmlemail = str_replace("##NAME_PLACEHOLDER##", $name, $htmlemail);

echo $htmlemail;

