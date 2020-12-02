<?php
        require 'header.php';
        require_once 'includes/functions.php';
		

        $floatDirection = "left";
        if (DIR=="rtl"){
            $floatDirection = "right";
        }

        $userid = $_SESSION['user_id'];
        $user = array();
       
		$SEL = "SELECT * FROM user WHERE id='$userid'";
        $res = $DB->query($SEL);
        if ($U = $res->fetch_assoc()){
			$user = $U;
		}else{
			$err = _("Error: Could not get user account.");
		}
		$email = trim( $user['email'] ); // "MyEmailAddress@example.com"
		$email = strtolower( $email ); // "myemailaddress@example.com"
		$emailhash = md5( $email );

		if (isset($_POST["submit"])){
			$interests = $DB->escape_string( $_POST["interests"] );
			$genre = $DB->escape_string( $_POST["genre"] );
			
			$newpassword =  $_POST["pass"] ;
			$newpassword2 =  $_POST["pass2"] ;

			$pass = "";
			if ($newpassword != "" && strlen($newpassword) > 3 && $newpassword == $newpassword2){
				$pass = $DB->real_escape_string(md5($newpassword));
			}elseif ($newpassword != ""){
				$err = _("Password is not long enough (4 characters) or is not the same as the second password.");
			}

			
		
			$UPDATESQL = "UPDATE user SET interests='$interests', genre_of_writing='$genre' WHERE id='$userid'";
			
			if ($pass != ""){
				$UPDATESQL = "UPDATE user SET interests='$interests', genre_of_writing='$genre', password='$pass'  WHERE id='$userid'";
			}
			$DB->query($UPDATESQL);
			$success = _("Preferences updated!");
			
		}

        include 'menu.php';
        ?>
        <div id="app-container">
            <?php if  (isset($err)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    <?php echo _("Warning!"); ?>
                </h4> <?php echo htmlspecialchars($err); ?>
            </div>
            <?php } ?>
            <?php if  (isset($success)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-success alert-dismissable">
                 
                <h4>
                    <?php echo _("Success!"); ?>
                </h4> <?php echo htmlspecialchars($success); ?>
            </div>
            <?php } ?>

            <div class="container">
            	<div class="md-12" style="height:60px;">
            	
            	</div>
                <div class="panel panel-success">
					<div class="panel-heading">
					  <a href="account.php" class="btn btn-primary" style="float:right;"><?php echo _("Back");?></a>
							
					  <h3 id="usernametitle" class="panel-title"><?php echo htmlspecialchars($user["name"]); ?></h3>
					  <h4 id="usernametype" class="panel-subtitle"><?php echo htmlspecialchars($user["type"]); ?></h4>
					</div>
					<div class="panel-body">
					  <div class="row">
						<div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="https://www.gravatar.com/avatar/<?php echo $emailhash;?>" class="img-circle profilepicture"> </div>
						
						<div class=" col-md-9 col-lg-9 "> 
							<form role="form" method="post" action="">
							  <div class="form-group AuthorSetting">
								<label for="user-genre"><?php echo _("Genre of writing"); ?></label>
								<input type="text" class="form-control" id="user-genre" name="genre" value="<?php echo htmlspecialchars($user['genre_of_writing']); ?>">
							  </div>
							  <div class="form-group">
								<label for="user-interests"><?php echo _("Interests"); ?></label>
								<input type="text" class="form-control" id="user-interests" name="interests" value="<?php echo htmlspecialchars($user['interests']); ?>">
							  </div>
							 
							  <div class="form-group">
								<label for="user-public_private"><?php echo _("Change password"); ?></label>
								<input type="password" class="form-control" id="change-password" name="pass" value=""><br/>
								<input type="password" class="form-control" id="change-password2" value="" name="pass2" placeholder="<?php echo _("type again"); ?>">
								<p><?php echo _("Leave blank to keep your current password"); ?></p>
							  </div>
								<input type="submit" class="btn btn-primary" name="submit" value="<?php echo _('Save');?>">
							</form>

							 	
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </body>
    <script>
    loadhome();
    </script>

</html>



					