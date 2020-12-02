<?php

        require 'header.php';
        require_once 'includes/functions.php';
        require_once 'includes/ebook_create.php';


        //new chapter
        if (isset($_GET['book']) && isset($_GET['action']) && $_GET['action']=="newchapter" ){
            $userid = $_SESSION['user_id'];
            $chaptertitle = "New Chapter";
            $bookid = $DB->real_escape_string($_GET['book']);
            $bookcontent = "";
            
            //select book from database
            $sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
            $res = $DB->query($sel);
            if ($B = $res->fetch_assoc()){

                //get chapter nr.
                $GETCHAPTERNR = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr DESC";
                $chapres = $DB->query($GETCHAPTERNR);
                if ($chap = $chapres->fetch_assoc()){
                    $chap_nr = $chap['chapter_nr'] + 1;
                }else{
                    $chap_nr = 0;
                }

                $chaptertitle = $chap_nr."-".$chaptertitle;
                writeChapterToFile("", $chaptertitle, $chap_nr, "",$B['rootUrl']);
                
                $INS = "INSERT INTO private_chapters (bookid,title,content, chapter_nr) VALUE('$bookid','$chaptertitle','','$chap_nr');";
                $DB->query($INS);

                generateTOC($DB,$bookid);
                
                $success = "Chapter added.";
                header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
                exit();
            }
        }

        //delete chapter
        if (isset($_POST['chapterid']) && isset($_POST['delete']) && isset($_POST['bookid']) ){
            $chapterid = $DB->real_escape_string($_POST['chapterid']);
            $DEL = "DELETE FROM private_chapters WHERE id='$chapterid';";
            $DB->query($DEL);
            $success = "Chapter deleted";
            $bookid = $DB->real_escape_string($_POST['bookid']);
            header('Location: ?book='.$bookid.'&success='.$success);
            exit();
        }

        //save chapter
        if (isset($_POST['bookid']) && isset($_POST['save']) && isset($_POST['chaptertitle'])){
            //save ebook chapter
            $userid = $_SESSION['user_id'];
            $chaptertitle = $DB->real_escape_string($_POST['chaptertitle']);
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $bookcontent = $_POST['bookcontent'];
            $chapterid = $DB->real_escape_string($_POST['chapterid']);
            
            //select book from database
            $sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
            $res = $DB->query($sel);
            if ($B = $res->fetch_assoc()){
                $bookcontent = str_replace($B['rootUrl']."OPS/","../",$bookcontent);

                //get chapter nr.
                $GETCHAPTERNR = "SELECT * FROM private_chapters WHERE id='$chapterid'";
                $chapres = $DB->query($GETCHAPTERNR);
                if ($chap = $chapres->fetch_assoc()){
                    $chap_nr = $chap['chapter_nr'];
                }else{
                    //error
                    $err = "Could not save chapter.";
                }


                writeChapterToFile($bookcontent, $chaptertitle, $chap_nr, "",$B['rootUrl']);
                $bookcontent =  $DB->real_escape_string($bookcontent);
                $INS = "UPDATE private_chapters SET title='$chaptertitle', content='$bookcontent' WHERE id='$chapterid';";
                $DB->query($INS);

                generateTOC($DB,$bookid);
                
                $success = "Chapter saved.";
            }else{
                $err = "Book not found or not from user.";
            }
        }


        //handle actions
        //Create new book
        if (isset($_POST['create']) && isset($_POST['booktitle'])){
            //create new ebook here
            $userid = $_SESSION['user_id'];
            $booktitle = $DB->real_escape_string($_POST['booktitle']);
            $author = $DB->real_escape_string($_POST['author']);
            $bookid = CreateNew($userid,$booktitle,$author,$_FILES,$SERVERURL,$DB);
        }
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
        }

        $chapterTitles = array();
        $chapterIds = array();
        $chapterContents = array();
        if (isset($book)){
            
            //get chapters and display first chapter
             $SEL = "SELECT * FROM private_chapters WHERE bookid='$bookid'";
             //print "loading chapters".$SEL;
             $ch = $DB->query($SEL);
             $currentchapter = 0;


             
             while ($C = $ch->fetch_assoc()){
                print "chapter:".$C['title'];
                $chapterTitles[] = $C['title'];
				$chapterNrs[] = $C['chapter_nr'];
                $chapterIds[] = $C['id'];
                $chapterContents[] = $C['content'];
             }
             if (isset($_REQUEST['cchapt'])){
                $cchapt = $_REQUEST['cchapt'];
                $currentchapter = array_search($cchapt, $chapterNrs);
             }
        }


        if (isset($_GET['success'])){
            $success = $_GET['success'];
        }
        if (isset($book)){
            echo '<script>
			
			var linklist = [';
			for($i = 0; $i < count($chapterTitles); ++$i) {
				echo '{title: "'.$chapterTitles[$i].'", value: "'.$chapterNrs[$i].'-chapter.xhtml'.'"},';
			}
			echo '];
			
			loadCreator("'.$book['rootUrl'].'OPS/assets/'.'", linklist); </script>';
        }

        include 'menu2.php';

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

            <div class="col-md-12" >
                <h2>Book: <?php echo $booktitle; ?></h2>
                 <form role="form" action="" id="create_form" method="post">
                    <input type="hidden" name="bookid" value="<?php echo $bookid;?>">
                    <input type="hidden" name="chapterid" value="<?php echo $chapterIds[$currentchapter];?>">
                    <div class="form-group">
                         <label for="Password">Chapter Title</label><input type="text" name="chaptertitle" class="form-control" id="Password" value="<?php echo $chapterTitles[$currentchapter];?>">
                    </div>

                    <textarea name="bookcontent" class="ebook-edit" style="width:100%; height:500px"><?php if (isset($chapterContents)){
                        echo str_replace("../",$book['rootUrl']."OPS/",$chapterContents[$currentchapter]);
                    }
                        ?></textarea>
                    <br/>
					 <button type="submit" name="save" id="savebtn" value="save" class="btn btn-primary">Save</button>
					 <?php echo '<a class="btn btn-default" target="_blank" href="http://bs-solutions.nl/embellisher/?epub='.$VIEWURL.$book['rootUrl'].'">Preview book</a>'; ?>
                   
                </form> 

                

                
            </div>
            
        </div>
    </body>

</html>
