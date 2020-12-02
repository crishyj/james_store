<?php
    ob_start();
    session_start();
    include '../api/config.php';
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['type']) || $_SESSION['type'] == -1) {
        header('Location: index.php');
        die();
    }

    if (isset($_SESSION['originalid']) && isset($_GET['return'])) {
        $_SESSION['user_id'] = $_SESSION['originalid'];
        $_SESSION['display_name'] = $_SESSION['originalname'];
        $_SESSION['type'] = 1;
        unset($_SESSION['originalid']);
        unset($_SESSION['originalname']);
    }

    if ($_SESSION['type'] == 1 && isset($_GET['id']) && isset($_GET['type'])) {
        $_SESSION['originalid'] = $_SESSION['user_id'];
        $_SESSION['originalname'] = $_SESSION['display_name'];
        $_SESSION['user_id'] = $_GET['id'];
        $_SESSION['display_name'] = $_GET['name'];
        $_SESSION['type'] = $_GET['type'];
    }

    $genres = array('', 'Drama', 'Fable', 'Fairy Tale', 'Fantasy', 'Fiction', 'Fiction in Verse', 'Folklore', 'Historical Fiction', 'Horror', 'Humor', 'Legend', 'Mystery', 'Mythology', 'Poetry', 'Realistic Fiction', 'Science Fiction', 'Short Story', 'Steampunk', 'Tall Tale', 'Biography/Autobiography', 'Essay', 'Narrative Nonfiction', 'Nonfiction', 'Speech');
?>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../images/embellisherereader_favicon.png" rel="shortcut icon" />
    <link href="../images/embellisherereader-touch-icon.png" rel="apple-touch-icon" />
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/viewer.css">
    <link rel="stylesheet" type="text/css" href="../css/branding.css">
    <link rel="stylesheet" type="text/css" href="../css/library.css">
    <link rel="stylesheet" type="text/css" href="../css/readium_js.css">
</head>

<body style="overflow:auto;">