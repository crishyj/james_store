<?php
ob_start();
session_start();
include 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['type']))
{
    header('Location: index.php'); 
    die();
}

$genres = array("","Drama","Fable","Fairy Tale","Fantasy","Fiction","Fiction in Verse","Folklore","Historical Fiction","Horror","Humor","Legend","Mystery","Mythology","Poetry","Realistic Fiction","Science Fiction","Short Story","Steampunk","Tall Tale","Biography/Autobiography","Essay","Narrative Nonfiction","Nonfiction","Speech");
   
    ?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="images/embellisherereader_favicon.png" rel="shortcut icon"/>
    <link href="images/embellisherereader-touch-icon.png" rel="apple-touch-icon"/>
        

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/viewer.css">
    <link rel="stylesheet" type="text/css" href="css/branding.css">
    <link rel="stylesheet" type="text/css" href="css/library.css">
    <link rel="stylesheet" type="text/css" href="css/readium_js.css">


    <link href="css/jasny-bootstrap.min.css" rel="stylesheet" media="screen">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="js/jquery-1.11.0.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jasny-bootstrap.min.js"></script>


    <script type="text/javascript" src="js/tinymce/tinymce.js"></script>
    <script type="text/javascript" src="js/creator.js"></script>

   



    </head>

    <!-- This is all application-specific HTML -->
    <body style="overflow:auto;">