<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_POST["email"] );
$bookid = $DB->escape_string( $_POST["bookid"] );
$promocode = $DB->escape_string( $_POST["code"] );





$result = array();

$GETBOOK = "SELECT * FROM library WHERE id='$bookid'";
$book = $DB->query($GETBOOK)->fetch_assoc();
$bookprice = floatval($book['price']);
$bookpricecents = intval( $bookprice * 100 );

if ($promocode && $promocode != ""){
	$SQLpromo = "SELECT * FROM promocodes WHERE code = '$promocode' AND bookid='$bookid' AND maxuses<>0 AND free=1";
	$RESpromo = $DB->query($SQLpromo);
	if ($promo = $RESpromo->fetch_assoc()){
		$id = $promo['id'];
		if ($promo['maxuses'] != "-1"){
			$newvalue = $promo['maxuses'] - 1;
			if ($newvalue == 0){
				$UPDATE = "DELETE FROM promocodes WHERE id='$id'";
			}else{
				$UPDATE = "UPDATE promocodes SET maxuses='$newvalue' WHERE id='$id'";
			}
			
			$DB->query($UPDATE);
		}
		$bookpricecents = 0;
	}
}



$SQL = "SELECT user.id as userid, allfree FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){

	if ($user['allfree']==1){
		$bookpricecents = 0;
	}

	if ($bookpricecents > 0){
		$result['error']="LOGIN";
		echo json_encode($result);
		exit();
	}
	
	$userid = $user['userid'];
	$deleteSQL = "INSERT INTO user_library (userid,libraryid) VALUES ('$userid','$bookid')";
	$DB->query($deleteSQL);
	$result['msg']="success";

}else{
	$result['error']="LOGIN";
}
echo json_encode($result);