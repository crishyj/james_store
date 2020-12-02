<?php
        require 'header.php';
        require_once 'includes/functions.php';

        if (isset($_POST['delete']) && isset($_POST['bookid'])){
            //create new ebook here
            $userid = $_SESSION['user_id'];
            $bookid = $DB->real_escape_string($_POST['bookid']);
            //delete ebook.
            $SEL = "SELECT * FROM private_library WHERE id='$bookid' AND userid='$userid'";
            $res = $DB->query($SEL);
            if ($B = $res->fetch_assoc()){
                $loc = $B['rootUrl'];
                rmdir_recursive($loc);
                $DEL = "DELETE FROM private_chapters WHERE bookid='$bookid'";
                $res = $DB->query($DEL);
                $DEL = "DELETE FROM private_library WHERE id='$bookid'";
                $res = $DB->query($DEL);
            }else{
                $err = "Could not delete ebook, this book does not exist or does not belong to you.";
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
            <div class="container">
                <div class="jumbotron">
                
                    <h2>Welcome at the Epub Creator Studio</h2>
                    <p>Create and manage your Epub3 books here.</p>

                </div>
                <div class="col-md-4 jumbotron" style="font-size:100%;">
                    <form role="form" action="creator.php" id="formnew" method="post" enctype="multipart/form-data"> 
                        <div class="form-group">
                             <label for="booktitle">Book Title</label><input type="text" name="booktitle" class="form-control" id="booktitle" required="required">
                        </div>
                         <div class="form-group">
                             <label for="booktitle">Author</label><input type="text" name="author" class="form-control" id="author" required="required">
                        </div>
                        <div class="form-group">
                             <label for="coverimage">Cover Image (optional)</label><input type="file" title="Browse cover image" name="coverimage" accept="image/*">
                             <p>* Only png, jpg or gif are allowed. Recommended size of 1024px x 800px.</p>
                        </div>
                        <button type="submit" name="create" value="new" class="btn btn-primary">Create new Ebook</button>
                    </form> 
                </div>
                <div class="col-md-8 list-view">


                    <h2>My Ebooks</h2>
                    <hr>
                    <?php
                    $userid = $_SESSION['user_id'];
                    $SEL = "SELECT * FROM private_library WHERE userid='$userid'";
                    $rb = $DB->query($SEL);
                    while ($B = $rb->fetch_assoc()){
                       
                        echo '<div class="col-xs-4 col-sm-3 col-md-2 library-item">
                                <div class="info-wrap">
                                   

                                <a href="creator.php?book='.$B['id'].'" type="button" style="background: none; border: none; padding: 0; margin: 0;" >';
                        if ($B['coverHref']!=""){
                            echo '<img aria-hidden="true" class="img-responsive" src="'.$B['coverHref'].'" alt="" /> ';
                        }else{
                             echo '<div aria-hidden="true" class="no-cover" style="background-image: url(\"images/cover1.jpg\")" ></div> ';
                        }
                                echo '
                                </a>
                                </div>
                                        
                               
                                <div class="caption book-info">
                                    <h4 class="title">'.$B['title'].'</h4>
                                    <div class="author">'.$B['author'].'</div>
                                    <div class="buttons">

                                      <a class="btn btn-primary" href="creator.php?book='.$B['id'].'">Edit book</a> 

                                      <a class="btn btn-default" target="_blank" href="http://bs-solutions.nl/embellisher/?epub='.$VIEWURL.$B['rootUrl'].'">View book</a> 
                                      <form style="float:right; margin-left:10px;" role="form" action="" id="delete_form" method="post" onsubmit="return confirm(\'Are you sure you want to delete this book? \nThis action cannot be undone.\')">
                                            <input type="hidden" name="bookid" value="'.$B['id'].'">
                                       
                                            <button type="submit" name="delete" id="deletebtn" value="delete" class="btn btn-danger">Delete</button>
                                        </form>   
                           
                                    </div>
                                     
                                    
                                </div>
                            </div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </body>

</html>
