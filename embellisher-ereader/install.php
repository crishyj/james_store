<?php
include 'api/config.php';

function TableExists($table, $DB) {
  $res = $DB->query("SHOW TABLES LIKE '$table'");
  return $res->num_rows > 0;
}
// Here's a startsWith function
function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

if (PREMIUM) {
    $licenseactivate = "http://emrepublishing.com/?edd_action=activate_license&item_id=7987&license=".urlencode($LICENSEKEY)."&url=".urlencode($SERVERURL);

    $activateres = file_get_contents($licenseactivate);
    $license_data = json_decode( $activateres );
    if( $license_data->license != 'valid' ) {
    	$install_error = "Your License is not valid! Please check api/config.php and enter a valid license there.";
    }
}


function run_sql_file($location, $DB){
    //load file
    $commands = file_get_contents($location);

    //delete comments
    $lines = explode("\n",$commands);
    $commands = '';
    foreach($lines as $line){
        $line = trim($line);
        if( $line && !startsWith($line,'--') ){
            $commands .= $line . "\n";
        }
    }

    //convert to array
    $commands = explode(";", $commands);

    //run commands
    $total = $success = 0;
    foreach($commands as $command){
        if(trim($command)){
            $success += ($DB->query($command)==false ? 0 : 1);
            $total += 1;
        }
    }

    //return number of successful queries and total number of queries found
    return array(
        "success" => $success,
        "total" => $total
    );
}



if (isset($_GET['continue']) && $_GET['continue']=="yes" && !TableExists('sessions',$DB)){
	//install sql
	$sql = run_sql_file('api/install.sql',$DB);

	$pass = $DB->real_escape_string(md5("admin"));
	$SQL = "INSERT INTO user (name,admin,email,password) VALUES ('admin','1', 'admin@admin.com','$pass')";
	$DB->query($SQL);
}

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


    </head>

    <!-- This is all application-specific HTML -->
    <body style="overflow:auto;">
    <nav id="app-navbar" class="navbar" role="navigation">
    <div class="btn-group navbar-left">
            <!-- <button type="button" class="btn btn-default icon icon-show-hide"></button>  -->
        <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
            <span class="icon-readium" aria-hidden="true"></span>
        </button>
        
       
    </div>
    <div class="btn-group navbar-right">
        
        <a  href="index.html" tabindex="1" type="button" class="btn icon-library" title="{{strings.view_library}} [{{keyboard.SwitchToLibrary}}]" aria-label="{{strings.view_library}}">
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        </a>

       
        <!-- <button type="button" class="btn btn-default icon icon-bookmark"></button>-->
    </div>
</nav>

        <div id="app-container">
		
			
            
            <div class="jumbotron">
			
				<?php if (!TableExists('sessions',$DB)){ ?>
                <div class="container">
				
                    <h1 style="font-size:28px;">Intallation</h1>
					  <?php if  (isset($install_error)) { ?>
						<!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
						<div class="alert alert-danger alert-dismissable">
							 
							<h4>
								ERROR!
							</h4> <?php echo $install_error; ?>
						</div>
						<?php } ?>
					
					
                    <p>Thank you for purchasing the Embellisher E-reader.<br/>
					If you read the <a href="README.txt">README.txt</a></b> and edited both config files, <br/>
					Click the button below to start installing the app.</p>
					 <?php if  (!isset($install_error)) { ?>
					<p><a class="btn btn-primary" href="?continue=yes">Start installation</a> </p>
					<?php } ?>
                </div>
				<?php } else { ?>
				 <div class="container">
				
                    <h1 style="font-size:28px;">Done!</h1>
					<?php if  (isset($install_error)) { ?>
						<!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
						<div class="alert alert-danger alert-dismissable">
							 
							<h4>
								ERROR!
							</h4> <?php echo $install_error; ?>
						</div>
						<?php } ?>
                    <p>The installation is finished! You can now use the Embellisher App and the <a href="admin/" target="_blank">admin interface</a><br/>
					<b>Username:</b> admin@admin.com<br/>
					<b>Password:</b> admin</p>
                </div>
				
				<?php } ?>
            </div>

           

                
            
        </div>
    </body>

</html>
