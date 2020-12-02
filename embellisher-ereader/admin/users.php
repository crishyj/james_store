<?php
        require 'header.php';
        if ($_SESSION['type']!=1){
            exit();
        }

        //handle user register
        //
        if (isset($_POST["massadd"])){
            ini_set("auto_detect_line_endings", "1");//needed for mac files
            $usersadded = 0;
            //open the uploaded csv file
            if (isset($_FILES["users"]["tmp_name"])){
                $fin = fopen($_FILES["users"]["tmp_name"],'r') or die('cant open file');
                $storeid = $_SESSION['user_id'];
                $admin = 0;
                $status = 0;
                $allfree = 0;
                $maxsessions = 1;
                $type = "Reader";
                $err = "";
                while (($data=fgetcsv($fin,1000,","))!==false) {
                    
                    if (count($data)<3){
                        $err .= "The csv file should at least contain <b>Name, email, password</b>.<br/>";
                        break;
                    }else{
                        $username = $DB->real_escape_string($data[1]);
                        if (!filter_var($username, FILTER_VALIDATE_EMAIL)){
                            $err .= $username." is not a valid email address.<br/>";
                            continue;
                        }
                        $name = $DB->real_escape_string($data[0]);
                        $password = $data[2];
                        if (strlen($password) < 5){
                            $err .= "Password for user $username is too short, should be 5 characters minimum.<br/>";
                            continue;
                        }
                        $SQL = "SELECT * FROM user WHERE email='$username'";
                        $RES = $DB->query($SQL);
                        if ($user = $RES->fetch_assoc()){
                            $err .= "User '$username' already exists.<br/>";
                        }else{
                            $pass = $DB->real_escape_string(md5($password));
                            $SQL = "INSERT INTO user (name,type,email,password,admin,status,maxsessions,allfree,storeid) VALUES ('$name','$type', '$username','$pass','$admin','$status','$maxsessions','$allfree','$storeid')";
                            $RES = $DB->query($SQL);
                            $userid = $DB->insert_id;
                            $GETBOOKS = "SELECT id FROM library WHERE preload=1";
                            $BOOKS = $DB->query($GETBOOKS);
                            
                            //Free epubs!
                            while ($book = $BOOKS->fetch_assoc()){
                                $bookid = $book['id'];
                                $SQLI = "INSERT INTO user_library (userid,libraryid) VALUES ('$userid','$bookid')";
                                $RESI = $DB->query($SQLI);
                            }
                            $usersadded ++;
                        }
                    }
                }
                fclose($fin);
                if (isset($err) && $err==""){
                    unset($err);
                }
                if ($usersadded > 0){
                    $success = $usersadded." users added successfully!";
                }
            }else{
                $err = "Please upload a csv file";
            }
        }
        if (isset($_POST["signup"])){
            if (!isset($_POST["password"]) || strlen($_POST["password"]) < 4){
                $err = "Please fill in a password with a minimum size of 4.";
                
            }
            if (!isset($_POST["username"])){
                $err = "Please fill in your email.";
                
            }
            if (!isset($_POST["name"])){
                $err = "Please fill in your name.";
            }
            if (!isset($_POST["maxsessions"])){
                $err = "Please fill max sessions in.";
            }
            if (!isset($_POST["admin"])){
                $err = "Please fill admin (0/1) in.";
            }
            $type = "Reader";
            if (isset($_POST["type"])){
                $type = $DB->real_escape_string( $_POST["type"] );
            }
            $storeid = $_SESSION['user_id'];
            $password = $_POST["password"] ;
            $username = $DB->real_escape_string( $_POST["username"] );
            $name = $DB->real_escape_string( $_POST["name"] );
            $admin = $DB->real_escape_string( $_POST["admin"] );
            $status = $DB->real_escape_string( $_POST["status"] );
            $allfree = $DB->real_escape_string( $_POST["allfree"] );
            
            $maxsessions = $DB->real_escape_string( $_POST["maxsessions"] );

            $SQL = "SELECT * FROM user WHERE email='$username'";
            $RES = $DB->query($SQL);
            if ($user = $RES->fetch_assoc()){
                $err = "User already exists.";
            }else{
                $pass = $DB->real_escape_string(md5($password));
                $SQL = "INSERT INTO user (name,type,email,password,admin,status,maxsessions,allfree,storeid) VALUES ('$name','$type', '$username','$pass','$admin','$status','$maxsessions','$allfree','$storeid')";
                $RES = $DB->query($SQL);
                $userid = $DB->insert_id;
				$GETBOOKS = "SELECT id FROM library WHERE preload=1";
				$BOOKS = $DB->query($GETBOOKS);
				
				//Free epubs!
				while ($book = $BOOKS->fetch_assoc()){
					$bookid = $book['id'];
					$SQLI = "INSERT INTO user_library (userid,libraryid) VALUES ('$userid','$bookid')";
					$RESI = $DB->query($SQLI);
				}
                $success = "User added successfully!";
            }
        }     
        if (isset($_POST["changeuser"])){
            
            
            $type = "Reader";
            if (isset($_POST["type"])){
                $type = $DB->real_escape_string( $_POST["type"] );
            }

            $passwordset = "";
            $password = $_POST["password"] ;
            $userid = $DB->real_escape_string( $_POST["userid"] );
            $username = $DB->real_escape_string( $_POST["username"] );
            $name = $DB->real_escape_string( $_POST["name"] );
            $admin = $DB->real_escape_string( $_POST["admin"] );
            $status = $DB->real_escape_string( $_POST["status"] );
            $allfree = $DB->real_escape_string( $_POST["allfree"] );

            $maxsessions = $DB->real_escape_string( $_POST["maxsessions"] );

            
            if ($password != ""){
                $password = $DB->real_escape_string(md5($password));
                $passwordset = ", password='".$password."'";
            }

            if ($_POST["changeuser"] == "delete"){
                $SQL = "DELETE FROM user WHERE id='$userid'";
                if ($RES = $DB->query($SQL)){
                    $success = "User removed successfully!";
                }else{
                    $err = "There was a problem in the sql".$DB->error;
                }
                

            }else{
                $SQL = "UPDATE user SET name='$name',type='$type',email= '$username' $passwordset,admin='$admin',status='$status',maxsessions='$maxsessions',allfree='$allfree' WHERE id='$userid'";
                if ($RES = $DB->query($SQL)){
                    $success = "User changed successfully!";
                }else{
                    $err = "There was a problem in the sql".$DB->error;
                }
            }
        }     

        include 'menu.php';
        ?>
        <div id="app-container">
            <?php if  (isset($err)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    Warning!
                </h4> <?php echo $err; ?>
            </div>
            <?php } ?>
            <?php if  (isset($success)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-success alert-dismissable">
                 
                <h4>
                    Success!
                </h4> <?php echo $success; ?>
            </div>
            <?php } ?>
            <div class="jumbotron">
                <div class="container">
                    <h1 style="font-size:28px;">User management</h1>
                    <p>Here you can view, add and change user accounts.</p>
                     
                </div>
            </div>

            <div class="col-md-4">
                <div id="tab-user-register" class="tab-pane active">

                    <div class="panel panel-info">
                        <div class="panel-heading">
                          <h3 class="panel-title">Add user</h3>
                        </div>
                        <div class="panel-body">                        
                            <form role="form" action="" method="POST">
                              <div class="form-group">
                                <label for="register-name">Name:</label>
                                <input type="text" class="form-control" id="register-name" value="" name="name" required="required">
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
                                <label for="register-maxsessions">Maximum sessions:</label>
                                <input type="number" class="form-control" id="register-maxsessions" name="maxsessions" value="1" required="required">
                              </div>
                              
                               <div class="form-group">
                                  <label for="registertype">Select type:</label>
                                  <select class="form-control" name="type" id="registertype">
                                    <option value="Reader" selected>Reader</option>
                                    <option value="Author">Author</option>
                                  </select>
                                </div>
                                <div class="form-group">
                                  <label for="registeradmin">Select account type:</label>
                                  <select class="form-control" name="admin" id="registeradmin">
                                    <option value="-1" selected>Read only user</option>
                                    <option value="0">Publisher</option>
                                    <option value="1">Admin</option>
                                  </select>
                                </div>
                                <input type="hidden" name="status" value="0">
                                <!--<div class="form-group">
                                  <label for="status">Select Creator Studio type:</label>
                                  <select class="form-control" name="status" id="status">
                                    <option value="0" >Standard Access</option>
                                    <option value="2" >Complete Access (with upload functions)</option>
                                  </select>
                                </div>-->
                                <div class="form-group">
                                  <label for="allfree">Select All books Free account:</label>
                                  <select class="form-control" name="allfree" id="allfree">
                                    <!--<option value="0" >No Access to Creator</option>-->
                                    <option value="0" >No</option>
                                    <option value="1" >Yes (all books are free for this user)</option>
                                  </select>
                                </div>
                             <button type="submit" name="signup" id="buttRegisterProfile" tabindex="1000" class="btn btn-primary" title="Sign up" aria-label="Sign up">Create account</button>
                            </form>
                            
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                          <h3 class="panel-title">Mass add user</h3>
                        </div>
                        <div class="panel-body">                        
                            <form role="form" action="" method="POST" enctype="multipart/form-data">
                              <div class="form-group">
                                <label for="epubfile">CSV file*:</label>
                                 <input type="file" title="Upload csv file" name="users" required="required">
                                  <p>* comma separated file with: <strong>username, email, password</strong><br/>
                                  Each line is one useraccount.</p>
                                 <?php 
                                //  if ($err && $err != ""){
                                //     echo '<p class="alert alert-danger">'.$err.'</p>';
                                //  }
                                 
                                 ?>
                              </div>
                              
                             <button type="submit" name="massadd" id="buttRegisterProfiles" tabindex="1000" class="btn btn-primary" title="Add users" aria-label="Sign up">Create accounts</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>

             <div class="col-md-8">
                <div id="tab-users" class="tab-pane active">

                    <div class="panel panel-success">
                        <div class="panel-heading">
                          <h3 class="panel-title">Active publishers</h3>
                        </div>
                        <div class="panel-body">    
                            <table class="table table-striped">  
                                <thead>  
                                  <tr>  
                                    <th>Name</th> 
                                    <th>E-mail</th> 
                                    <th style="display:none;">Type</th> 
                                    <th>Account</th> 
                                    <th style="display:none;">Creator Studio</th>  
                                    <th style="display:none;">Interests</th>  
                                    <th style="display:none;">Max sessions</th>
                                    <th>All Free</th>
                                    <th>$/m</th>
                                    <th>$/y</th>
                                    <th>Register date</th>
                                    <th></th>
                                  </tr>  
                                </thead>  
                                <tbody>                    
                            <?php
                                $sqlpub = "SELECT distinct owner FROM library";
                                if (!$RESpub = $DB->query($sqlpub)){
                                    echo "error in sql:".$DB->error;
                                    exit();
                                }
                                $publishers = array();
                                while ($p = $RESpub->fetch_assoc()){
                                    $publishers[] = $p['owner'];
                                }
                                $pubs = "(".implode(",", $publishers).")";
                                if ($pubs == "()"){
                                    $pubs = "(-1)";
                                }
                                
                                $SQL = "SELECT * FROM user WHERE id IN $pubs ORDER BY registerdate";
                                if ($RES = $DB->query($SQL)){
                                    while ($user = $RES->fetch_assoc()){

                                        $year_sales = 0;
                                        $month_sales = 0;
                                        $uid = $user['id'];
                                        $SQL2 = "SELECT SUM(t.price) as monthsales FROM library as l, transactions as t WHERE l.owner = '$uid' and l.id = t.libraryid and t.transactiondate >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) ORDER BY t.transactiondate DESC";
                                        $RES2 = $DB->query($SQL2);
                                        if ($sold = $RES2->fetch_assoc()){
                                            if ($sold['monthsales'] != "")
                                                $month_sales = floatval($sold['monthsales'])/100;
                                        }
                                        $SQL2 = "SELECT SUM(t.price) as yearsales FROM library as l, transactions as t WHERE l.owner = '$uid' and l.id = t.libraryid and YEAR(t.transactiondate) = YEAR(CURDATE()) ORDER BY t.transactiondate DESC";
                                        $RES2 = $DB->query($SQL2);
                                        if ($sold = $RES2->fetch_assoc()){
                                            if ($sold['yearsales'] != "")
                                                $year_sales = floatval($sold['yearsales'])/100;
                                        }


                                        echo '<tr>';  
                                        echo '<td><a href="edituser.php?id='.$user['id'].'">'.$user['name'].'</a></td> ';
                                        echo '<td><a href="mailto:'.$user['email'].'">mail</a></td> ';
                                        echo '<td style="display:none;">'.$user['type'].'</td> ';
                                        $acc = "Publisher";
                                        if ($user['admin']==1){
                                            $acc = "Admin";
                                        }elseif ($user['admin']==-1){
                                            $acc = "Read only";
                                        } 
                                        echo '<td>'.$acc.'</td> '; 
                                        
                                        $acc2 = "Standard";
                                        if ($user['status']==1){
                                            $acc2 = "Standard";
                                        }if ($user['status']==2){
                                            $acc2 = "Full Access";
                                        }
                                        echo '<td style="display:none;">'.$acc2.'</td> '; 
                                        echo '<td style="display:none;">'.$user['interests'].'</td>  ';
                                        echo '<td style="display:none;">'.$user['maxsessions'].'</td>';
                                        $allf = "No";
                                        if ($user['allfree']==1){
                                            $allf = "Yes";
                                        }
                                        echo '<td>'.$allf.'</td> ';
                                        echo '<td>$ '.$month_sales.'</td> ';
                                        echo '<td>$ '.$year_sales.'</td> ';
                                        echo '<td>'.$user['registerdate'].'</td> ';
                                        echo '<td><a href="edituser.php?id='.$user['id'].'" class="btn btn-warning">Edit</a></td>';
                                        echo '</tr> ';
                                    }
                                }
                            ?>
                                </tbody> 
                            </table>
                            
                        </div>
                    </div>

                    <?php if (SEPARATE_ADMINS){ ?>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                          <h3 class="panel-title">Your readers</h3>
                        </div>
                        <div class="panel-body">    
                            <table class="table table-striped">  
                                <thead>  
                                  <tr>  
                                    <th>Name</th> 
                                    <th>E-mail</th> 
                                    <th>Type</th> 
                                    <th>Account</th> 
                                    <th style="display:none;">Creator Studio</th>  
                                    <th>Interests</th>  
                                    <th style="display:none;">Max sessions</th>
                                    <th>All Free</th>
                                    <th></th>
                                  </tr>  
                                </thead>  
                                <tbody>                    
                            <?php
                                $storeid = $_SESSION['user_id'];
                                $yourreaders_emails = array();
                                $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid=$storeid ORDER BY name";
                                
                                $RES = $DB->query($SQL);
                                while ($user = $RES->fetch_assoc()){

                                    
                                    echo '<tr>';  
                                    echo '<td><a href="edituser.php?id='.$user['id'].'">'.$user['name'].'</a></td> ';
                                    echo '<td><a href="mailto:'.$user['email'].'">mail</a></td> ';
                                    $yourreaders_emails[] = $user['email'];
                                    echo '<td>'.$user['type'].'</td> ';
                                    $acc = "Publisher";
                                    if ($user['admin']==1){
                                        $acc = "Admin";
                                    }elseif ($user['admin']==-1){
                                        $acc = "Read only";
                                    } 
                                    echo '<td>'.$acc.'</td> '; 
                                    
                                    $acc2 = "Standard";
                                    if ($user['status']==1){
                                        $acc2 = "Standard";
                                    }if ($user['status']==2){
                                        $acc2 = "Full Access";
                                    }
                                    echo '<td style="display:none;">'.$acc2.'</td> '; 
                                    echo '<td>'.$user['interests'].'</td>  ';
                                    echo '<td style="display:none;">'.$user['maxsessions'].'</td>';
                                    $allf = "No";
                                    if ($user['allfree']==1){
                                        $allf = "Yes";
                                    }
                                    echo '<td>'.$allf.'</td> ';
                                    
                                    echo '<td><a href="edituser.php?id='.$user['id'].'" class="btn btn-warning">Edit</a></td>';
                                    echo '</tr> ';
                                }
                            ?>
                                </tbody> 
                            </table>
                            <label>Your readers' email addresses:</label>
                            <input type=text value="<?php echo implode("; ", $yourreaders_emails); ?>">
                            
                        </div>
                    </div>

                    <?php } ?>
                    

                    <div class="panel panel-info">
                        <div class="panel-heading">
                          <h3 class="panel-title">Other users</h3>
                        </div>
                        <div class="panel-body">    
                            <table class="table table-striped">  
                                <thead>  
                                  <tr>  
                                    <th>Name</th> 
                                    <th>E-mail</th> 
                                    <th>Type</th> 
                                    <th>Account</th> 
                                    <th style="display:none;">Creator Studio</th>  
                                    <th>Interests</th>  
                                    <th style="display:none;">Max sessions</th>
                                    <th>All Free</th>
                                    <th>Job</th>
                                    <th></th>
                                  </tr>  
                                </thead>  
                                <tbody>                    
                            <?php

                                

                                $SQL = "SELECT * FROM user WHERE id NOT IN $pubs ORDER BY name";
                                if (SEPARATE_ADMINS){ 
                                    $storeid = $_SESSION['user_id'];
                                    if($_SESSION['user_name'] == 'corporate@emrepublishing.com'){
                                        $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid <> $storeid AND category = 'Corporate' ORDER BY name";
                                    }
                                    elseif($_SESSION['user_name'] == 'education@emrepublishing.com'){
                                        $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid <> $storeid AND category = 'Education' ORDER BY name";
                                    }
                                    elseif($_SESSION['user_name'] == 'faithbased@emrepublishing.com'){
                                        $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid <> $storeid AND category = 'Faith-Based' ORDER BY name";
                                    }
                                    elseif($_SESSION['user_name'] == 'government@emrepublishing.com'){
                                        $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid <> $storeid AND category = 'Government' ORDER BY name";
                                    }
                                    elseif($_SESSION['user_name'] == 'healthcare@emrepublishing.com'){
                                        $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid <> $storeid AND category = 'Healthcare' ORDER BY name";
                                    }
                                    else{
                                        $SQL = "SELECT * FROM user WHERE id NOT IN $pubs AND storeid <> $storeid ORDER BY name";
                                    }
                                    
                                }
                                $all_emails = array();
                                $RES = $DB->query($SQL);
                                while ($user = $RES->fetch_assoc()){

                                    $all_emails[] = $user['email'];
                                    echo '<tr>';  
                                    echo '<td><a href="edituser.php?id='.$user['id'].'">'.$user['name'].'</a></td> ';
                                    echo '<td><a href="mailto:'.$user['email'].'">mail</a></td> ';
                                    echo '<td>'.$user['type'].'</td> ';
                                    $acc = "Publisher";
                                    if ($user['admin']==1){
                                        $acc = "Admin";
                                    }elseif ($user['admin']==-1){
                                        $acc = "Read only";
                                    } 
                                    echo '<td>'.$acc.'</td> '; 
                                    
                                    $acc2 = "Standard";
                                    if ($user['status']==1){
                                        $acc2 = "Standard";
                                    }if ($user['status']==2){
                                        $acc2 = "Full Access";
                                    }
                                    echo '<td style="display:none;">'.$acc2.'</td> '; 
                                    echo '<td>'.$user['interests'].'</td>  ';
                                    echo '<td style="display:none;">'.$user['maxsessions'].'</td>';
                                    $allf = "No";
                                    if ($user['allfree']==1){
                                        $allf = "Yes";
                                    }
                                    echo '<td>'.$allf.'</td> ';
                                    echo '<td>'.$user['job'].'</td>';
                                    
                                    echo '<td><a href="edituser.php?id='.$user['id'].'" class="btn btn-warning">Edit</a></td>';
                                    echo '</tr> ';
                                }
                            ?>
                                </tbody> 
                            </table>
                             <label>Other users' email addresses:</label>
                            <input type=text value="<?php echo implode("; ", $all_emails); ?>">
                        </div>
                    </div>
                </div>
            </div>
                
            
        </div>
    </body>

</html>
