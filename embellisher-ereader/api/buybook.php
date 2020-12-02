<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
require_once('lib/Stripe.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_POST["email"] );
$bookid = $DB->escape_string( $_POST["epubid"] );

$promocode = $DB->escape_string( $_POST["promocode"] );

$SQL = "SELECT user.id as userid FROM user,sessions WHERE email='$username' AND user.id = sessions.userid AND sessions.sessionid = '$session'";
$RES = $DB->query($SQL);
$result = array();
$GETBOOK = "SELECT * FROM library WHERE id='$bookid'";
$book = $DB->query($GETBOOK)->fetch_assoc();
$bookprice = floatval($book['price']);
$bookpricecents = intval( $bookprice * 100 );
$owner = $book['owner'];


if ($promocode && $promocode != ""){
	$SQLpromo = "SELECT * FROM promocodes WHERE code = '$promocode' AND bookid='$bookid' AND maxuses<>0";
	$RESpromo = $DB->query($SQLpromo);
	if ($promo = $RESpromo->fetch_assoc()){
		//code accepted!
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
		$bookpricecents =  intval($bookpricecents * (100.0-$promo['discount']) / 100.0);
		//$result['msg'] = "new price: ".$bookpricecents;
		//echo json_encode($result);
		//exit();
	}
}

if ($user = $RES->fetch_assoc()){
	//user logged in, we can continue with the payment.

	// Set your secret key: remember to change this to your live secret key in production
	// See your keys here https://dashboard.stripe.com/account
	
	//get stripe key
	$stripekey = "";
	$getkey = "SELECT stripe_private FROM user WHERE id='$owner'";
	$keyres = $DB->query($getkey);
	$stripekey = $keyres->fetch_assoc()['stripe_private'];


	Stripe::setApiKey($stripekey); // test key sk_test_6LTjOpSFemMB4APoZYAIG7PO

	// Get the credit card details submitted by the form
	$token = $_POST['token'];

	// Create the charge on Stripe's servers - this will charge the user's card
	try {
		$charge = Stripe_Charge::create(array(
		  "amount" => $bookpricecents, // amount in cents, again
		  "currency" => "usd",
		  "card" => $token,
		  "description" => $book['title'])
		);
		$userid = $user['userid'];
		$addtransaction = "INSERT INTO transactions (userid, libraryid,price) VALUES ('$userid','$bookid','$bookpricecents')";
		$DB->query($addtransaction);
		$addSQL = "INSERT INTO user_library (userid,libraryid) VALUES ('$userid','$bookid')";
		$DB->query($addSQL);
		$result['msg'] = "success";

	} catch(Stripe_CardError $e) {
		$result['msg'] = "card error";
	  // The card has been declined
	}

}
else{
	//user needs to login!
	$result['msg'] = "LOGIN";
}
echo json_encode($result);