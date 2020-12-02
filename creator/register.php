<?php
include 'config.php';
ob_start();
session_start();

if (isset($_POST['signup'])) {
    if (!isset($_POST['password']) || strlen($_POST['password']) < 4) {
        $err = 'Please fill in a password with a minimum size of 4.';
        echo json_encode($result);
        exit();
    }
    if (!isset($_POST['username'])) {
        $err = 'Please fill in your email.';
        echo json_encode($result);
        exit();
    }
    if (!isset($_POST['name'])) {
        $err = 'Please fill in your name.';
        echo json_encode($result);
        exit();
    }
    $type = 'Reader';
    if (isset($_POST['type'])) {
        $type = $DB->real_escape_string($_POST['type']);
    }
    $password = $_POST['password'];
    $username = $DB->real_escape_string($_POST['username']);
    $name = $DB->real_escape_string($_POST['name']);

    $SQL = "SELECT * FROM user WHERE email='$username'";
    $RES = $DB->query($SQL);
    if ($user = $RES->fetch_assoc()) {
        $err = 'User already exists.';
    } else {
        $pass = $DB->real_escape_string(md5($password));
        $SQL = "INSERT INTO user (name,type,email,password) VALUES ('$name','$type', '$username','$pass')";
        $RES = $DB->query($SQL);
        $userid = $DB->insert_id;
        $GETBOOKS = 'SELECT id FROM library WHERE preload=1';
        $BOOKS = $DB->query($GETBOOKS);

        //Free epubs!
        while ($book = $BOOKS->fetch_assoc()) {
            $bookid = $book['id'];
            $SQL = "INSERT INTO user_library (userid,libraryid) VALUES ('$userid','$bookid')";
            $RES = $DB->query($SQL);
        }

        $result['msg'] = 'success';
        //we signed up succesfully, lets give a welcome message

        $GET_WELCOME_EMAIL = 'SELECT * FROM emailtemplates WHERE id=2';
        if ($R = $DB->query($GET_WELCOME_EMAIL)) {
            if ($WELCOME_EMAIL = $R->fetch_assoc()) {
                $to = $username;
                $subject = $WELCOME_EMAIL['name'];
                $email_from = 'noreply@emrepublishing.com';
                $htmlemail = $WELCOME_EMAIL['content'];
                $htmlemail = str_replace('##EMAIL_PLACEHOLDER##', $username, $htmlemail);
                $htmlemail = str_replace('##NAME_PLACEHOLDER##', $name, $htmlemail);
                $headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion()."\r\nContent-type: text/html\r\n";
                @mail($to, $subject, $htmlemail, $headers);
            }
        }
        //succesful signup so go to login.
        echo '
            <html>
            <head>
            <link rel="stylesheet"  media="all" href="../css/bootstrap.css" />
            <META HTTP-EQUIV="refresh" CONTENT="1;URL=home.php">
            
            
            <title>Register</title>
            </head>
            <body>
            <div style="margin-top:10%;">
            
            <div class="center">
            <center> 
            <img src="images/about-embellisher-logo.png"></img><br/><br/>
            <h2>You are registered, login to continue..</h2>
            
          
            </center>
            </div>
            
            
            </div>
            </body>
            </html>';
    }
}

if (isset($_SESSION['user_id']) && isset($_SESSION['type']) && isset($_SESSION['status'])) {
    header('Location:home.php'); /* Stuur de browser naar www.site.nl */
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
        
	<title>Register</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/viewer.css">
    <link rel="stylesheet" type="text/css" href="css/branding.css">
    <link rel="stylesheet" type="text/css" href="css/library.css">
    <link rel="stylesheet" type="text/css" href="css/readium_js.css">

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40181201-10', 'auto');
  ga('send', 'pageview');

</script>


    </head>

    <!-- This is all application-specific HTML -->
    <body>
        <nav id="app-navbar" class="navbar" role="navigation">
            <div class="btn-group navbar-left">
                    <!-- <button type="button" class="btn btn-default icon icon-show-hide"></button>  -->
                <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
                    <span class="icon-readium" aria-hidden="true"></span>
                </button>
                
               
            </div>
            <div class="btn-group navbar-right">
                
                
                <a href="../" tabindex="1" type="button" class="btn icon-library" title="home" aria-label="home">
                    <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                </a>
               
                <!-- <button type="button" class="btn btn-default icon icon-bookmark"></button>-->
            </div>
        </nav>
        <div id="app-container">
            <?php if (isset($err)) {
        ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    Error!
                </h4> <?php echo $err; ?>
            </div>
            <?php
    } ?>

            <div class="jumbotron">
				<h2 class="header">Register Embellisher Account</h2>
			</div>
			<div class="container">
												
							<form role="form" method="POST" action="">
							  <div class="form-group">
								<label for="register-name">Name:</label>
								<input type="text" class="form-control" id="register-name" name="name" value="" required="required">
							  </div>
							  <div class="form-group">
								<label for="register-email">E-mail:</label>
								<input type="email" class="form-control" id="register-email" name="username" value="" required="required">
							  </div>
							  <div class="form-group">
								<label for="register-password">Password:</label>
								<input type="password" class="form-control" id="register-password" name="password" value="" required="required">
							  </div>
							  <div class="form-group">
                                 <label for="registertype">Publish or read?</label>
                                 <select class="form-control" name="type" id="registertype">
                                    <option value="Reader">Reader</option>
                                    <option value="Author" selected="selected">Author/Publisher</option>
                                 </select>
                              </div>
                              <div class="checkbox">
							    <label>
							      <input name="EULA" id="registereula" type="checkbox" value="1"> I have read and accept the <a href="EULA.pdf" target="_blank">EULA</a>
							    </label>
							  </div>
							 
							 <a id="buttClose" tabindex="1000" type="button" class="btn btn-default" href="index.php">Back to login</a>
							 <button type="submit" id="buttRegisterProfile" name="signup" value="1" tabindex="1000" class="btn btn-primary" title="Sign up" aria-label="Sign up">Sign up</button>
							</form>
							
						 </div>
        </div>
    </body>

</html>
<?php

?>