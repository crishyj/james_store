<?php
ob_start();
session_start();
include 'config.php';


if (isset($_SESSION['originalid']) && isset($_GET['return'])){
    //restore admin.
    $_SESSION['user_id'] = $_SESSION['originalid'];
    $_SESSION['display_name'] = $_SESSION['originalname'];
    $_SESSION['type'] = 1;
    unset($_SESSION['originalid']);
    unset($_SESSION['originalname']);
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['type'])  )
{
    header('Location: index.php'); 
    die();
}

if (!isset($_SESSION['status']) || $_SESSION['status']==""){
  $_SESSION['status'] = 0;
}
$u_id = $_SESSION['user_id'];
$SQL = "SELECT * FROM user WHERE id = '$u_id'";
$result = $DB->query($SQL);
while ($epub = $result->fetch_assoc()) {
    $new_genres = $epub['genres'];
}
if($new_genres ==''){
  $genres = array("","Drama","Fable","Fairy Tale","Fantasy","Fiction","Fiction in Verse","Folklore","Historical Fiction","Horror","Humor","Legend","Mystery","Mythology","Poetry","Realistic Fiction","Science Fiction","Short Story","Steampunk","Tall Tale","Biography/Autobiography","Essay","Narrative Nonfiction","Nonfiction","Speech");
}
else{
  $genres = explode(',', $new_genres);
  array_unshift($genres, '');
}
   
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="images/embellisherereader_favicon.png" rel="shortcut icon"/>
    <link href="images/embellisherereader-touch-icon.png" rel="apple-touch-icon"/>
        
    <meta name="description" content="Create eBooks online with interactive and rich content. Create, modify and view ebub3 books and download them to your computer.">
    <meta name="keywords" content="epub,epub3,mobi,ebook,editor,html5">
    <meta name="author" content="van Stein en Groentjes B.V.">
    
    <link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox.css">
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
    <script type="text/javascript" src="js/fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/jquery.ddslick.min.js"></script>
    <script type="text/javascript" src="js/creator.js"></script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40181201-10', 'auto');
  ga('send', 'pageview');

</script>


    <title>ePub Creator Studio</title>



    </head>

    <!-- This is all application-specific HTML -->
    <body style="overflow:auto;">
	
<?php
if (isset($_SESSION['originalid'])){
?>
<div class="alert alert-danger alert-dismissable">
                 
    <h4>
        Warning!
    </h4> 
    You are logged in as another client, <a href="home.php?return=1">return to the admin area</a>.
</div>

<?php
}
?>



<div class="panel panel-info" style="position:fixed; bottom:-10px;right:10px;z-index:99999999; background-color:white;">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Live chat
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body" style="height:420px;overflow:hidden;">
        <iframe id="chatframe" src="<?php echo $SERVERURL;?>phpfreechat/index.php" style="height:400px;">
        </iframe>
      </div>
    </div>
  </div>