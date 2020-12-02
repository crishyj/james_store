<?php
ini_set('max_execution_time', 6000); //6000 seconds = 100 minutes
ob_start();
session_start();
include 'config.php';
require_once 'includes/functions.php';
require_once 'includes/ebook_create.php';

include('mpdf60/mpdf.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['type']))
{
    header('Location: index.php'); 
    die();
}


if (isset($_GET['book'])){
   $bookid = $DB->real_escape_string($_GET['book']);
}
if (isset($bookid)){
	
	////////OTHER WAY
	
	generateOneFile($DB,$bookid,$_SESSION['user_id']);

	$url = $SERVERURL."ebooks/htmlbook".$bookid.".html";# "http://thetricky.net/mySQL/GROUP%20BY%20vs%20ORDER%20BY";
	//echo $url;

	$html = file_get_contents($url);

	$mpdf=new mPDF();
	$mpdf->WriteHTML($html);
	$mpdf->Output();
	
	exit;

	
	//Get title and make it a 12 character long filename
	$userid = $_SESSION['user_id'];
	$sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
    $res = $DB->query($sel);
    if ($B = $res->fetch_assoc()){
    	$content->set("title", $B['title']);
		$content->set("author", $B['author']);

    }

    $mobi->setContentProvider($content);


    $title = $B['title'];
	if($title === false) $title = "file";
	$title = urlencode(str_replace(" ", "_", strtolower(substr($title, 0, 12))));

	//echo $mobi->getData();
	//Send the mobi file as download
	$mobi->download($title.".mobi");
	die;
}