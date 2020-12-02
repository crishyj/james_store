<?php
include '../api/config.php';
ob_start();
session_start();

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; ++$i) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

if (!empty($_POST['email'])) {
    $username = $DB->real_escape_string($_POST['email']);
    $result = $DB->query("SELECT id,email,password,name,admin FROM user WHERE email = '".$username."'");

    if ($rij = $result->fetch_assoc()) {
        $newpassori = generateRandomString(8);
        $newpass = $DB->real_escape_string(md5($newpassori));
        $uid = $rij['id'];
        $UPDATESQL = "UPDATE user SET password='$newpass' WHERE id='$uid'";
        $DB->query($UPDATESQL);
        $to = $rij['email'];
        $name = $rij['name'];
        $email_from = 'noreply@emrepublishing.com';
        $headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n".'X-Mailer: PHP/'.phpversion()."\r\nContent-type: text/plain\r\n";
        $subject = 'Password reset Embellisher';
        $body = 'Dear '.$name.",
		\n\nYou requested to reset your password.
		\nYour new password is: ".$newpassori."
		\n\nKind Regards,\nThe Embellisher Ereader Team ";
        if (!mail($to, $subject, $body, $headers)) {
            $err = 'Email could not be send, contact the website admin.';
        }
    } else {
        $err = 'There is no account with this email address';
    }
}
if (empty($_POST['email']) || isset($err)) {
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
<body>
    <nav id="app-navbar" class="navbar" role="navigation">
        <div class="btn-group navbar-left">
            <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal"
                data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
                <span class="icon-readium" aria-hidden="true"></span>
            </button>
        </div>
        <div class="btn-group navbar-right">
            <a href="../" tabindex="1" type="button" class="btn icon-library" title="home" aria-label="home">
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
            </a>
        </div>
    </nav>
    <div id="app-container">
        <?php if (isset($err)) {
        ?>
        <div class="alert alert-danger alert-dismissable">
            <h4>
                Error!
            </h4> <?php echo $err; ?>
        </div>
        <?php
    } ?>
        <div class="jumbotron">
            <h2>Password Reset</h2>
            <p>You can reset your password here. Enter your email address to continue.</p>
            <form role="form" action="" id="formlogin" method="post">
                <div class="form-group">
                    <label for="email">E-mail</label><input type="email" name="email" class="form-control" id="email">
                </div>
                <button type="submit" name="login" value="login" class="btn btn-primary">Reset password</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
} else {
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

<body>
    <nav id="app-navbar" class="navbar" role="navigation">
        <div class="btn-group navbar-left">
            <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal"
                data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
                <span class="icon-readium" aria-hidden="true"></span>
            </button>
        </div>
        <div class="btn-group navbar-right">
            <a href="../" tabindex="1" type="button" class="btn icon-library" title="home" aria-label="home">
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
            </a>
        </div>
    </nav>
    <div id="app-container">
        <?php if (isset($err)) {
            ?>
        <div class="alert alert-danger alert-dismissable">
            <h4>
                Error!
            </h4> <?php echo $err; ?>
        </div>
        <?php
        } ?>

        <div class="jumbotron">
            <h2>Success!</h2>
            <p>A new password has been send to your email address.</p>
        </div>
    </div>
</body>
</html>



<?php
    }?>