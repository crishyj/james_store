<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
$promocode = $DB->escape_string( $_POST["code"] );
$bookid = $DB->escape_string( $_POST["bookid"] );


$SQL = "SELECT * FROM promocodes WHERE code = '$promocode' AND bookid='$bookid' AND maxuses<>0";
$RES = $DB->query($SQL);
$result = array();


if ($promo = $RES->fetch_assoc()){
	$result['msg'] = "success";
	$result['free'] = $promo['free'];
	$result['discount'] = $promo['discount'];
}
else{
	$result['msg'] = "no such code for this book";
}
echo json_encode($result);