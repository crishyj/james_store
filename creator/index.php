<?php
include 'config.php';
ob_start();
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['type']) && isset($_SESSION['status'])) {
    header('Location:home.php');
}

if (!empty($_POST['login'])) {
    $username = $DB->real_escape_string($_POST['username']);
    $result = $DB->query("SELECT id,email,password,admin,status FROM user WHERE email = '".$username."'");
    $count = $result->num_rows;
    $rij = $result->fetch_assoc();

    function validate_name($username)
    {
        $u_length = strlen($username);
        if ($u_length <= '3' or $username = '') {
            return false;
        } else {
            return true;
        }
    }

    function validate_login($username, $password, $password2, $count)
    {
        global $salt;
        if ($count != '1') {
            return false;
        } else {
            $password = md5($password);
            if ($password2 != $password) {
                return false;
            } else {
                return true;
            }
        }
    }

    if (!validate_name($_POST['username'])) {
        $err = 'Incorrect password';
    } elseif (!validate_login($_POST['username'], $_POST['password'], $rij['password'], $count)) {
        $err = 'Incorrect password';
    }

    if (isset($err)) {
    } else {
        $password = $_POST['password'];
        $tijd = 60 * 150;
        if (!setcookie('user_id', $rij['id'], time() + $tijd, '/embellishercreator/')) {
            echo 'cookie failed, contact bas@vansteinengroentjes.nl to fix this issue.';
            exit();
        }
        if (!setcookie('user_pass', $password, time() + $tijd, '/embellishercreator/')) {
            echo 'cookie failed, contact bas@vansteinengroentjes.nl to fix this issue.';
            exit();
        }
        $_SESSION['user_id'] = $rij['id'];
        $_SESSION['user_name'] = $rij['email'];
        $_SESSION['type'] = $rij['admin'];
        $_SESSION['status'] = $rij['status'];
        echo '
            <html>
                <head>
                    <link rel="stylesheet"  media="all" href="../css/bootstrap.css" />
                    <META HTTP-EQUIV="refresh" CONTENT="1;URL=home.php">
                    <title>Log in</title>
                </head>
                <body>
                    <div style="margin-top:10%;">
                        <div class="center">
                            <center> 
                                <img src="images/about-embellisher-logo.png"></img><br/><br/>
                                <h2>You are logged in, please wait..</h2>
                            </center>
                        </div>
                    </div>
                </body>
            </html>';
    }
}
if (empty($_POST['login']) || isset($err)) {
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
        <title>Log in</title>
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

    <body>
        <nav id="app-navbar" class="navbar" role="navigation">
            <div class="btn-group navbar-left">
                <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
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

                <h2>Welcome to the ePub Creator Studio</h2>
                <p>Login with your Embellisher Ereader account to continue.<br/>
				If you arrived here from another Ereader application, sign up for your free Embellisher account using the Signup button below.</p>
              

                <form role="form" action="" id="formlogin" method="post">
                    <div class="form-group">
                         <label for="Username">E-mail</label><input type="email" name="username" class="form-control" id="Username">
                    </div>
                    <div class="form-group">
                         <label for="Password">Password</label><input type="password" name="password" class="form-control" id="Password">
                    </div>
                    
                    <button type="submit" name="login" value="login" class="btn btn-primary">Login</button>

                    <a href="register.php" target="_blank" name="register" class="btn btn-default">Signup for your free Embellisher account.</a><br/>
                    <a href="resetpass.php" target="_blank" name="reset"><?php echo _('Forgot your password?'); ?></a>
                </form>
            </div>
        </div>
    </body>

</html>
<?php
}?>