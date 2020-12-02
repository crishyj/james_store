<?php
$DB = new mysqli("mysql03.totaalholding.nl", "bssoluti_test", "gtBvn&U0xUJZ", "bssoluti_ereader"); //bssoluti_test gtBvn&U0xUJZ
if ($DB->connect_errno) {
	echo "Failed to connect to MySQL: (" . $DB->connect_errno . ") " . $DB->connect_error;
}
$DB->set_charset("utf8");
$salt = '$2a$07$hallothisisa22stringha$';

$stripeKey = "sk_live_F5fvqmJdFbtH6rRnR4VUJbW1";

$VIEWURL = "../creator/";
?>
