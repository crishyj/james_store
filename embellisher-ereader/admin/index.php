<?php
include '../api/config.php';
ob_start();
session_start();
$password = '';

if (isset($_SESSION['user_id']) && isset($_SESSION['type']) && $_SESSION['type'] != -1) {
    header('Location:home.php');
}

if (!empty($_POST['login'])) {
    $username = $DB->real_escape_string($_POST['username']);
    // $result = $DB->query("SELECT id,email,password,admin,name,status FROM user WHERE email = '".$username."'");
    $result = $DB->query("SELECT * FROM user WHERE email = '".$username."'");
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
            $passwordori = $password;
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
        $tijd = 60 * 150;
        if (!setcookie('user_id', $rij['id'], time() + $tijd, '/embellisheradmin/')) {
            echo 'cookie failed, contact bas@vansteinengroentjes.nl to fix this issue.';
            exit();
        }
        if (!setcookie('user_pass', $password, time() + $tijd, '/embellisheradmin/')) {
            echo 'cookie failed, contact bas@vansteinengroentjes.nl to fix this issue.';
            exit();
        }
        if ($rij['admin'] == -1) {
            $err = 'You do not have enough rights to login on this page.';
        } else {
            $_SESSION['user_id'] = $rij['id'];
            $_SESSION['user_name'] = $rij['email'];
            $_SESSION['display_name'] = $rij['name'];
            $_SESSION['type'] = $rij['admin'];
            $_SESSION['status'] = $rij['status'];            
            $_SESSION['category'] = $rij['category'];
            $_SESSION['job'] = $rij['job'];
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
                                    <img src="../images/about-embellisher-logo.png"></img><br/><br/>
                                    <h2>You are logged in, please wait..</h2>
                                </center>
                            </div>
                        </div>
                    </body>
                </html>';
        }
    }
}

if (PREMIUM) {
    $ch = 'http://emrepublishing.com/?edd_action=check_license&item_id=7987&license='.urlencode($LICENSEKEY).'&url='.urlencode($SERVERURL);

    $r = file_get_contents($ch);
    $license_data = json_decode($r);
    if ($license_data->license != 'valid') {
        echo 'Your license is not valid or expired, please get a valid license at http://emrepublishing.com';
        exit();
    }
}
if (empty($_POST['login']) || isset($err)) {
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
            <h2>Welcome to the Embellisher Publishers Panel.</h2>
            <p>Re-enter your username and password to continue.</p>
            <form role="form" action="" id="formlogin" method="post">
                <div class="form-group">
                    <label for="Username">E-mail</label><input type="email" name="username" class="form-control"
                        id="Username">
                </div>
                <div class="form-group">
                    <label for="Password">Password</label><input type="password" name="password" class="form-control"
                        id="Password">
                </div>
                <button type="submit" name="login" value="login" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
}?>