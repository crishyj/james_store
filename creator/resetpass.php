<?php
include 'config.php';
ob_start();
session_start();

error_reporting(-1);
ini_set('display_errors', 'On');

function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (!empty($_POST['email'])) {
    $username = $DB->real_escape_string($_POST['email']);
    $result  = $DB->query("SELECT id,email,password,name,admin FROM user WHERE email = '".$username."'");
    
    
    if ($rij = $result->fetch_assoc()){
		$newpassori = generateRandomString(8);
		$newpass = $DB->real_escape_string(md5($newpassori));
		$uid = $rij['id'];
		$UPDATESQL = "UPDATE user SET password='$newpass' WHERE id='$uid'";
		$DB->query($UPDATESQL);
		
		$to = $rij['email'];
		$name = $rij['name'];
				
		$email_from = EMAIL_FROM;

		//set headers
		$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n" .'X-Mailer: PHP/' . phpversion()."\r\nContent-type: text/plain; charset=UTF-8\r\n";
		
		//set subject
		$subject = _("Password reset Mendele Books Editor");
		
		//create body for email
		$body = _("Dear ").$name.",\n\n"._("You requested to reset your password.\nYour new password is: ").$newpassori.
        "\n\n"._("Kind Regards,\nThe Mendele support team\nemail: support@mendele.co.il\nphone: +972-(0)9-9563293\n");
		if (! mail($to, $subject, $body, $headers)){
			$err = _("Email could not be send, contact the website admin.");
		}
	}else{
		$err = _("There is no account with this email address");
	}
}
if (empty($_POST['email']) || isset($err))
{
   
    ?>

<html dir="<?php echo DIR ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="images/embellisherereader_favicon.png" rel="shortcut icon"/>
    <link href="images/embellisherereader-touch-icon.png" rel="apple-touch-icon"/>

    <meta name="description" content="Create eBooks online with interactive and rich content. Create, modify and view ebub3 books and download them to your computer.">
    <meta name="keywords" content="epub,epub3,mobi,ebook,editor,html5">
    <meta name="author" content="van Stein en Groentjes B.V.">
        
    <title><?php echo _("Log in");?></title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/viewer.css">
    <link rel="stylesheet" type="text/css" href="css/branding.css">
    <link rel="stylesheet" type="text/css" href="css/library.css">
    <link rel="stylesheet" type="text/css" href="css/readium_js.css">
    </head>

    <!-- This is all application-specific HTML -->
     <body>
        <nav id="app-navbar" class="navbar" role="navigation">
            <div class="btn-group navbar-right">
                    <!-- <button type="button" class="btn btn-default icon icon-show-hide"></button>  -->
                <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="<?php echo _("Epub Creator Studio"); ?>" aria-label="<?php echo _("Epub Creator Studio"); ?>">
                    <span class="icon-readium" aria-hidden="true"></span>
                </button>
                
               
            </div>
            <div class="btn-group navbar-left">
                
                
                <a href="<?php echo HOME_LINK ?>" tabindex="1" type="button" class="btn icon-library" title="<?php echo _("home"); ?>" aria-label="<?php echo _("home"); ?>">
                    <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                </a>
               
                <!-- <button type="button" class="btn btn-default icon icon-bookmark"></button>-->
            </div>
        </nav>
        <div id="app-container">
            <?php if  (isset($err)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    <?php echo _("Error!");?>
                </h4> <?php echo $err; ?>
            </div>
            <?php } ?>

            <div class="jumbotron">

                <h2><?php echo _("Password Reset")?></h2>
                <p><?php echo _("You can reset your password here. Enter your email address to continue.");?></p>
              

                <form role="form" action="" id="formlogin" method="post">
                    <div class="form-group">
                         <label for="email"><?php echo _("E-mail");?></label><input type="email" name="email" class="form-control" id="email">
                    </div>
                    <button type="submit" name="login" value="login" class="btn btn-primary"><?php echo _("Reset password");?></button>
                </form>
            </div>
        </div>
    </body>

</html>
<?php

}else{
?>

<html dir="<?php echo DIR ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="images/embellisherereader_favicon.png" rel="shortcut icon"/>
    <link href="images/embellisherereader-touch-icon.png" rel="apple-touch-icon"/>

    <meta name="description" content="Create eBooks online with interactive and rich content. Create, modify and view ebub3 books and download them to your computer.">
    <meta name="keywords" content="epub,epub3,mobi,ebook,editor,html5">
    <meta name="author" content="van Stein en Groentjes B.V.">
        
    <title><?php echo _("Log in");?></title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/viewer.css">
    <link rel="stylesheet" type="text/css" href="css/branding.css">
    <link rel="stylesheet" type="text/css" href="css/library.css">
    <link rel="stylesheet" type="text/css" href="css/readium_js.css">




    </head>

    <!-- This is all application-specific HTML -->
    <body>
        <nav id="app-navbar" class="navbar" role="navigation">
            <div class="btn-group navbar-right">
                    <!-- <button type="button" class="btn btn-default icon icon-show-hide"></button>  -->
                <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="<?php echo _("Epub Creator Studio"); ?>" aria-label="<?php echo _("Epub Creator Studio"); ?>">
                    <span class="icon-readium" aria-hidden="true"></span>
                </button>
                
               
            </div>
            <div class="btn-group navbar-left">
                
                
                <a href="<?php echo HOME_LINK ?>" tabindex="1" type="button" class="btn icon-library" title="<?php echo _("home"); ?>" aria-label="<?php echo _("home"); ?>">
                    <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                </a>
               
                <!-- <button type="button" class="btn btn-default icon icon-bookmark"></button>-->
            </div>
        </nav>
        <div id="app-container">
            <?php if  (isset($err)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    <?php echo _("Error!");?>
                </h4> <?php echo $err; ?>
            </div>
            <?php } ?>

            <div class="jumbotron">

                <h2><?php echo _("Success!");?></h2>
                <p><?php echo _("A new password has been send to your email address.");?></p>
             
            </div>
        </div>
    </body>

</html>



<?php

}?>