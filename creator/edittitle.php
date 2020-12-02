<?php

        require 'header.php';
        require_once 'includes/functions.php';
        require_once 'includes/ebook_create.php';


       

      
        if (isset($_GET['book'])){
            $bookid = $DB->real_escape_string($_GET['book']);
        }
        if (isset($bookid)){
            
            $SEL = "SELECT * FROM private_library WHERE id='$bookid'";
            $rb = $DB->query($SEL);
            if ($B = $rb->fetch_assoc()){
                $booktitle = $B['title'];
                $book = $B;
            }
        }else{
            //return 
        }


        if (isset($_GET['success'])){
            $success = $_GET['success'];
        }
       

        ?>
         

        <div class="container" id="app-container" style="margin-top:50px;">
            <?php if  (isset($err)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    Warning!
                </h4> <?php echo $err; ?>
            </div>
            <?php } ?>
            <div id="alerts">
                <?php if (isset($success)){
                   echo '<div class="alert alert-success alert-dismissable">' .
                    '<button type="button" class="close" data-dismiss="alert">' .
                    '&times;</button>' . $success . '</div>';
                }?>
            </div>

            <div class="col-md-12 jumbotron" style="font-size:100%;">
                <form role="form" action="creator.php" id="formnew" method="post" enctype="multipart/form-data"> 
                    <input type="hidden" name="book_id" value="<?php echo $bookid; ?>">
                    <div class="form-group">
                         <label for="booktitle">Book Title</label><input type="text" name="booktitle" class="form-control" id="booktitle" required="required" value="<?php echo $booktitle; ?>">
                    </div>
                     <div class="form-group">
                         <label for="author">Author</label><input type="text" name="author" class="form-control" id="author" required="required" value="<?php echo htmlentities($book['author']); ?>">
                    </div>
                    <!--<div class="form-group">
                         <label for="lang">Language (2 letter code)</label><input type="text" name="lang" class="form-control" id="lang" required="required" value="<?php echo htmlentities($book['lang']); ?>">
                    </div>
                    <div class="form-group">
                         <label for="pagination">Pagination direction (rtl or ltr)</label><input type="text" name="pagination" class="form-control" id="pagination" required="required" value="<?php echo htmlentities($book['pagination']); ?>">
                    </div>-->
                    <img src="<?php echo $book['coverHref']; ?>" style="height:120px; max-width:100%;">
                    <div class="form-group">
                         <label for="coverimage">Cover Image (only if you want to overwrite the original)</label><input type="file" title="Browse cover image" name="coverimage" accept="image/*">
                         <p>* Only png, jpg or gif are allowed. Recommended size of 1024px x 800px.</p>
                    </div>
                    <button type="submit" name="action" value="updatename" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Save </button>
                </form> 
            </div>
            
        </div>
    </body>

</html>
