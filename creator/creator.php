<?php
        require 'header.php';
        require_once 'includes/functions.php';
        require_once 'includes/ebook_create.php';
        // if (isset($_GET[debug])) {
        //     error_reporting(E_ALL);
        //     ini_set('display_errors', '1');
        // }

        function updateTemplates($DB, $bookid, $userid)
        {
            $sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
            $res = $DB->query($sel);
            if ($B = $res->fetch_assoc()) {
                $templatelocations = array('ebooks/empty/OPS/assets/', 'ebooks/family/OPS/assets/', 'ebooks/child/OPS/assets/', 'ebooks/castle/OPS/assets/');
                $newbooklocation = $B['rootUrl'].'OPS/assets/';
                //echo $newbooklocation;
                $template = $B['template'];
                $emptybooklocation = $templatelocations[$template];
                $goodluck = xcopy($emptybooklocation.'js/', $newbooklocation.'js/');
                xcopy($emptybooklocation.'css/', $newbooklocation.'css/');
            } else {
                echo 'An error has occured while updateing the template. Please contact info@vansteinengroentjes.nl';
                exit();
            }
        }

        function saveCompleteBook($DB, $bookid, $userid, $APP_LOC)
        {
            updateTemplates($DB, $bookid, $userid);
            $sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
            $res = $DB->query($sel);
            if ($B = $res->fetch_assoc()) {

                $GETSONGS = "SELECT * FROM private_audio WHERE bookid='$bookid'";
                $songs = $DB->query($GETSONGS);
                $song_titles = array();
                $song_files = array();
                while ($song = $songs->fetch_assoc()) {
                    $song_titles[] = $song['name'];
                    $song_files[] = $song['audiofile'];
                }
                
                //get videos
                $GETVIDEOS = "SELECT * FROM private_video WHERE bookid='$bookid'";
                $videos = $DB->query($GETVIDEOS);
                $video_titles = array();
                $video_files = array();
                while ($video = $videos->fetch_assoc()) {
                    $video_titles[] = $video['name'];
                    $video_files[] = $video['videofile'];
                }

                //get vimeos
                $GETVIMEOS = "SELECT * FROM private_vimeo WHERE bookid='$bookid'";
                $vimeos = $DB->query($GETVIMEOS);
                $vimeo_titles = array();
                $vimeo_links = array();
                while ($vimeo = $vimeos->fetch_assoc()) {
                    $vimeo_titles[] = $vimeo['name'];
                    $vimeo_links[] = $vimeo['vimeoLink'];
                }

                $logicnumber = 0;
                $GETchapters = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
                $chapters = $DB->query($GETchapters);
                while ($chap = $chapters->fetch_assoc()) {
                    $chap_nr = $logicnumber;
                    $chap_id = $chap['id'];
                    $sqlsave = "UPDATE private_chapters SET chapter_nr = '$chap_nr' WHERE id='$chap_id'";
                    $DB->query($sqlsave);
                    writeChapterToFile($chap['content'], $chap['title'], $chap_nr, '', $B['rootUrl'], $song_titles, $song_files, $video_titles, $video_files, $vimeo_titles, $vimeo_links, $APP_LOC);
                    ++$logicnumber;
                }
                generateTOC($DB, $bookid);
            }
        }

        //add song to playlist
        if (isset($_POST['addSong']) && isset($_POST['bookid'])) {
            $userid = $_SESSION['user_id'];
            $songtitle = $DB->real_escape_string($_POST['songtitle']);
            $songlocation = $DB->real_escape_string($_POST['songlocation']);
            $bookid = $DB->real_escape_string($_POST['bookid']);

            $INS = "INSERT INTO private_audio (bookid,name,audiofile) VALUE('$bookid','$songtitle','$songlocation');";
            $DB->query($INS);

            saveCompleteBook($DB, $bookid, $userid, $APP_LOC);

            $success = 'Added '.$songtitle.' to playlist.';
            header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
            exit();
        }
         //delete song
        if (isset($_POST['audioid']) && isset($_POST['deleteAudio']) && isset($_POST['bookid'])) {
            $audioid = $DB->real_escape_string($_POST['audioid']);
            $DEL = "DELETE FROM private_audio WHERE id='$audioid';";
            $DB->query($DEL);
            $success = 'Deleted '.$songtitle.' from playlist.';
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $userid = $_SESSION['user_id'];

            saveCompleteBook($DB, $bookid, $userid, $APP_LOC);

            header('Location: ?book='.$bookid.'&success='.$success);
            //TODO save complete book.
            exit();
        }

          //add video to playlist
        if (isset($_POST['addVideo']) && isset($_POST['bookid'])) {
            $userid = $_SESSION['user_id'];
            $videotitle = $DB->real_escape_string($_POST['videotitle']);
            $videolocation = $DB->real_escape_string($_POST['videolocation']);
            $bookid = $DB->real_escape_string($_POST['bookid']);

            if (strripos($videolocation, 'youtu.be')) {
                echo"
                    <script>
                        alert('Please check the Video Type');
                        window.history.back();
                    </script>";
                exit();
            } else {
                $INS = "INSERT INTO private_video (bookid,name,videofile) VALUE('$bookid','$videotitle','$videolocation');";
                $DB->query($INS);
                saveCompleteBook($DB, $bookid, $userid, $APP_LOC);
                $success = 'Added '.$videotitle.' to playlist.';
                header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
                exit();
            }

            // $INS = "INSERT INTO private_video (bookid,name,videofile) VALUE('$bookid','$videotitle','$videolocation');";
            // $DB->query($INS);

            // saveCompleteBook($DB, $bookid, $userid, $APP_LOC);

            // $success = 'Added '.$videotitle.' to playlist.';
            // header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
            // exit();
        }
         //delete video
        if (isset($_POST['videoid']) && isset($_POST['deleteVideo']) && isset($_POST['bookid'])) {
            $videoid = $DB->real_escape_string($_POST['videoid']);
            $DEL = "DELETE FROM private_video WHERE id='$videoid';";
            $DB->query($DEL);
            $success = 'Deleted '.$videotitle.' from playlist.';
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $userid = $_SESSION['user_id'];

            saveCompleteBook($DB, $bookid, $userid, $APP_LOC);

            header('Location: ?book='.$bookid.'&success='.$success);
            //TODO save complete book.
            exit();
        }

         //add vimeo to playlist
         if (isset($_POST['addVimeo']) && isset($_POST['bookid'])) {
             $userid = $_SESSION['user_id'];
             $vimeotitle = $DB->real_escape_string($_POST['vimeotitle']);
             $vimeoLink = $DB->real_escape_string($_POST['vimeoLink']);
             $bookid = $DB->real_escape_string($_POST['bookid']);

             if (strripos($vimeoLink, 'youtu.be')) {
                 $youtubePos = strripos($vimeoLink, 'youtu.be') + 9;
                 $youtube0 = substr($vimeoLink, $youtubePos);
                 $youtube = 'https://www.youtube.com/embed/'.rtrim($youtube0);
                 $INS = "INSERT INTO private_vimeo (bookid,name,vimeoLink) VALUE('$bookid','$vimeotitle','$youtube');";
                 $DB->query($INS);
                 saveCompleteBook($DB, $bookid, $userid, $APP_LOC);
                 $success = 'Added '.$videotitle.' to playlist.';
                 header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
                 exit();
             } else {
                 echo"
                    <script>
                        alert('Please check the Video Type');
                        window.history.back();
                    </script>";
                 exit();
             }

             // $INS = "INSERT INTO private_video (bookid,name,videofile) VALUE('$bookid','$videotitle','$videolocation');";
            // $DB->query($INS);

            // saveCompleteBook($DB, $bookid, $userid, $APP_LOC);

            // $success = 'Added '.$videotitle.' to playlist.';
            // header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
            // exit();
         }
         //delete video
        if (isset($_POST['vimeoid']) && isset($_POST['deleteVimeo']) && isset($_POST['bookid'])) {
            $vimeoid = $DB->real_escape_string($_POST['vimeoid']);
            $DEL = "DELETE FROM private_vimeo WHERE id='$vimeoid';";
            $DB->query($DEL);
            $success = 'Deleted '.$vimeotitle.' from playlist.';
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $userid = $_SESSION['user_id'];
            saveCompleteBook($DB, $bookid, $userid, $APP_LOC);
            header('Location: ?book='.$bookid.'&success='.$success);
            exit();
        }

         //edit title etc
        if (isset($_POST['book_id']) && isset($_POST['action']) && $_POST['action'] == 'updatename') {
            $userid = $_SESSION['user_id'];
            $booktitle = $DB->real_escape_string($_POST['booktitle']);
            $author = $DB->real_escape_string($_POST['author']);
            $bookid = $DB->real_escape_string($_POST['book_id']);
            if (ChangeBook($bookid, $userid, $booktitle, $author, $_FILES, $SERVERURL, $DB)) {
                $success = 'Book meta info changed succesfuly!';
            } else {
                $error = 'There was a problem saving the book meta info, please try again.';
            }
        }

        //CHANGE CHAPTER ORDER
        if (isset($_GET['book']) && isset($_GET['action']) && isset($_GET['chapter_id'])) {
            $userid = $_SESSION['user_id'];
            $bookid = $DB->real_escape_string($_GET['book']);

            $chapter_id = $DB->real_escape_string($_GET['chapter_id']);

            //select book from database
            $sel = "SELECT * FROM private_chapters WHERE bookid='$bookid' AND id='$chapter_id'";
            $res = $DB->query($sel);
            if ($C = $res->fetch_assoc()) {
                $previous = $C['chapter_nr'];
                if ($_GET['action'] == 'up' && $previous > 0) {
                    $new_nr = $previous - 1;
                } elseif ($_GET['action'] == 'down') {
                    $new_nr = $previous + 1;
                }
                $FIX_OTHER = "UPDATE private_chapters SET chapter_nr='$previous' WHERE chapter_nr='$new_nr' AND bookid='$bookid'";
                $DB->query($FIX_OTHER);
                $FIX_NEW = "UPDATE private_chapters SET chapter_nr='$new_nr' WHERE id='$chapter_id' AND bookid='$bookid'";
                $DB->query($FIX_NEW);

                saveCompleteBook($DB, $bookid, $userid, $APP_LOC);
                $success = 'Chapter moved to position:'.$new_nr;
                header('Location: ?book='.$bookid.'&cchapt='.$new_nr.'&success='.$success);
                exit();
            }
        }

        //new chapter
        if (isset($_GET['book']) && isset($_GET['action']) && $_GET['action'] == 'newchapter') {
           
            $bookid = $DB->real_escape_string($_GET['book']);
           
            $sel = "SELECT * FROM private_library WHERE id='$bookid'";
            $res = $DB->query($sel);
            if ($B = $res->fetch_assoc()) {
                $userid = $B['userid'];
                $chaptertitle = 'New Chapter';
                $bookcontent = '';
                updateTemplates($DB, $bookid, $userid);
                
                //get chapter nr.
                $GETCHAPTERNR = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr DESC";
                $chapres = $DB->query($GETCHAPTERNR);
                if ($chap = $chapres->fetch_assoc()) {
                    $chap_nr = $chap['chapter_nr'] + 1;
                } else {
                    $chap_nr = 0;
                }
                
                $chaptertitle = $chap_nr.'-'.$chaptertitle;
              
                $newcontent = '<div id="container-normal"><h1>(Title here)</h1><br/><p>(Paragraph content here)</p></div>';
                if ($B['template'] == 1) {
                    $newcontent = '<p style="text-align: center;"><img style="display: block; margin-left: auto; margin-right: auto;" src="../assets/images/header.png" alt="" width="985" height="65" />-(page)-</p><div id="container-normal"><h1>(Title here)</h1><br/><p>(Paragraph content here)</p></div>';
                }
                
                writeChapterToFile('', $chaptertitle, $chap_nr, $newcontent, $B['rootUrl']);
                
                $newcontent = $DB->real_escape_string($newcontent);
                
                $INS = "INSERT INTO private_chapters (bookid,title,content, chapter_nr) VALUE('$bookid','$chaptertitle','$newcontent','$chap_nr')";
                // var_dump($INS);
                // die();
                $DB->query($INS);
                generateTOC($DB, $bookid);
                $success = 'Chapter added.';
                header('Location: ?book='.$bookid.'&cchapt='.$chap_nr.'&success='.$success);
                exit();
            }
        }

        //delete chapter
        if (isset($_POST['chapterid']) && isset($_POST['delete']) && isset($_POST['bookid'])) {
            $chapterid = $DB->real_escape_string($_POST['chapterid']);
            $DEL = "DELETE FROM private_chapters WHERE id='$chapterid'";
            $DB->query($DEL);
            $success = 'Chapter deleted';
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $userid = $_SESSION['user_id'];
            saveCompleteBook($DB, $bookid, $userid, $APP_LOC);
            header('Location: ?book='.$bookid.'&success='.$success);
            exit();
        }

        //save chapter
        if (isset($_POST['bookid']) && isset($_POST['save']) && isset($_POST['chaptertitle'])) {
            
            $userid = $_SESSION['user_id'];
            $chaptertitle = $DB->real_escape_string($_POST['chaptertitle']);
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $bookcontent = $_POST['bookcontent'];
            $chapterid = $DB->real_escape_string($_POST['chapterid']);

            $sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
            $res = $DB->query($sel);
            if ($B = $res->fetch_assoc()) {
                $bookcontent = str_replace($APP_LOC.'/'.$B['rootUrl'].'OPS/', '../', $bookcontent);
                $bookcontent = str_replace($B['rootUrl'].'OPS/', '../', $bookcontent);
                $GETCHAPTERNR = "SELECT * FROM private_chapters WHERE id='$chapterid'";
                $chapres = $DB->query($GETCHAPTERNR);
                if ($chap = $chapres->fetch_assoc()) {
                    $chap_nr = $chap['chapter_nr'];
                } else {
                    $err = 'Could not save chapter.';
                }

                $GETSONGS = "SELECT * FROM private_audio WHERE bookid='$bookid'";
                $songs = $DB->query($GETSONGS);
                $song_titles = array();
                $song_files = array();
                while ($song = $songs->fetch_assoc()) {
                    $song_titles[] = $song['name'];
                    $song_files[] = $song['audiofile'];
                }

                //get videos
                $GETVIDEOS = "SELECT * FROM private_video WHERE bookid='$bookid'";
                $videos = $DB->query($GETVIDEOS);
                $video_titles = array();
                $video_files = array();
                while ($video = $videos->fetch_assoc()) {
                    $video_titles[] = $video['name'];
                    $video_files[] = $video['videofile'];
                }

                //get vimeos
                $GETVIMEOS = "SELECT * FROM private_vimeo WHERE bookid='$bookid'";
                $vimeos = $DB->query($GETVIMEOS);
                $vimeo_titles = array();
                $vimeo_links = array();
                while ($vimeo = $vimeos->fetch_assoc()) {
                    $vimeo_titles[] = $vimeo['name'];
                    $vimeo_links[] = $vimeo['vimeoLink'];
                }

                writeChapterToFile($bookcontent, $chaptertitle, $chap_nr, '', $B['rootUrl'], $song_titles, $song_files, $video_titles, $video_files, $vimeo_titles, $vimeo_links, $APP_LOC);
                $bookcontent = $DB->real_escape_string($bookcontent);
                $INS = "UPDATE private_chapters SET title='$chaptertitle', content='$bookcontent' WHERE id='$chapterid';";
                $DB->query($INS);
                generateTOC($DB, $bookid);
                $success = 'Chapter saved.';
            } else {
                $err = 'Book not found or not from user.';
            }
        }

        if (isset($_POST['create']) && isset($_POST['booktitle'])) {
            $userid = $_SESSION['user_id'];
            $booktitle = $DB->real_escape_string($_POST['booktitle']);
            $template = $DB->real_escape_string($_POST['template']);
            $lang = $DB->real_escape_string($_POST['lang']);
            $pagination = $DB->real_escape_string($_POST['pagination']);
            $author = $DB->real_escape_string($_POST['author']);
            $bookid = CreateNew($userid, $booktitle, $author, $_FILES, $SERVERURL, $DB, $template, $lang, $pagination);
        }
        if (isset($_GET['book'])) {
            $bookid = $DB->real_escape_string($_GET['book']);
        }
        if (isset($bookid)) {
            $userid = $_SESSION['user_id'];
            $SEL = "SELECT * FROM private_library WHERE id='$bookid' AND userid='$userid'";
            $rb = $DB->query($SEL);
            if ($B = $rb->fetch_assoc()) {
                $booktitle = $B['title'];
                $book = $B;
            }
        }

        $chapterTitles = array();
        $chapterIds = array();
        $chapterContents = array();
        if (isset($book)) {
            $SEL = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
            $ch = $DB->query($SEL);
            $currentchapter = 0;

            while ($C = $ch->fetch_assoc()) {
                $chapterTitles[] = $C['title'];
                $chapterNrs[] = $C['chapter_nr'];
                $chapterIds[] = $C['id'];
                $chapterContents[] = $C['content'];
            }
            if (isset($_REQUEST['cchapt'])) {
                $cchapt = $_REQUEST['cchapt'];
                $currentchapter = array_search($cchapt, $chapterNrs);
            }
        }

        $audioTitles = array();
        $audioIds = array();
        $audioSources = array();
        if (isset($book)) {
            //get songs
            $SEL = "SELECT * FROM private_audio WHERE bookid='$bookid'";
            $ch = $DB->query($SEL);
            while ($A = $ch->fetch_assoc()) {
                $audioTitles[] = $A['name'];
                $audioIds[] = $A['id'];
                $audioSources[] = $A['audiofile'];
            }
        }

        $videoTitles = array();
        $videoIds = array();
        $videoSources = array();
        if (isset($book)) {
            //get songs
            $SEL = "SELECT * FROM private_video WHERE bookid='$bookid'";
            $ch = $DB->query($SEL);
            while ($A = $ch->fetch_assoc()) {
                $videoTitles[] = $A['name'];
                $videoIds[] = $A['id'];
                $videoSources[] = $A['videofile'];
            }
        }

        $vimeoTitles = array();
        $vimeoIds = array();
        $vimeoSources = array();
        if (isset($book)) {
            //get songs
            $SEL = "SELECT * FROM private_vimeo WHERE bookid='$bookid'";
            $ch = $DB->query($SEL);
            while ($A = $ch->fetch_assoc()) {
                $vimeoTitles[] = $A['name'];
                $vimeoIds[] = $A['id'];
                $vimeoSources[] = $A['vimeoLink'];
            }
        }

        $videoelement = '<video class="video-js vjs-default-skin" controls preload="auto" width="640" height="264" poster="" data-setup="{}"> <source src="http://video-js.zencoder.com/oceans-clip.mp4" type="video/mp4" /></video>';
        $videoelementfamily = '<video class="video-js vjs-family-skin"  controls preload="auto" width="640" height="264"  poster="'.$book['rootUrl'].'OPS/assets/images/time%20capsule.png" data-setup="{}"> <source src="http://video-js.zencoder.com/oceans-clip.mp4" type="video/mp4" /></video>';
        $imagetemplatefamily = '<p><img style="width: 50%; height: auto; float: left;" src="//upload.wikimedia.org/wikipedia/commons/4/40/Family_jump.jpg" alt="Family photo 1" /><img style="width: 48%; margin-left: 2%; height: auto; float: left;" src="//affirmation.org/wp-content/uploads/2013/11/stock-photo-family.jpg" alt="Photo 2" /></p><p style="width: 50%; height: auto; float: left; text-align: center;"><em>Caption 1: Family photo</em></p><p style="width: 48%; margin-left: 2%; height: auto; float: left; text-align: center;"><em>Caption 2: Another Family Photo</em></p><hr style="clear: both;" /><p><strong>REPLACE PHOTOS AFTER INSERTION</strong></p>';
        $bigimage = '<p><img style="width: 100%; height: auto;" src="//upload.wikimedia.org/wikipedia/commons/4/40/Family_jump.jpg" alt="Family photo 1" /></p><p style="width: 100%; height: auto;  text-align: center;"><em>Caption 1: Family photo</em></p><hr/><p><strong>REPLACE PHOTO AFTER INSERTION</strong></p>';
        $childbigimage = '<p><img style="max-width: 100%; height: auto;" src="'.$book['rootUrl'].'OPS/assets/images/princess.png" alt="" /></p><p style="width: 100%; height: auto;  text-align: center;"><p><strong>REPLACE PHOTO AFTER INSERTION</strong></p>';
        $princesstitle = '<h1 class="princess">Princess Title</h1>';

        if (isset($_GET['success'])) {
            $success = $_GET['success'];
        }
        if (isset($book)) {
            echo '<script>';
            $template = $book['template'];
            echo '
            var templatelist = [';
            if ($template == 0) {
                echo '{title: \'Numbered Page\', content: \'<div id="container-normal"><h1 style="text-align:center">Title</h1><p style="text-align:center">~(number)~</p><br/><p>Place here your content.</p></div>\'},
                {title: \'Normal page\', content: \'<div id="container-normal"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Reverse Page\', content: \'<div id="container-reverse"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Forevermore Page\', content: \'<div id="container-steam"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Two photos row\', content: \''.$imagetemplatefamily.'\'},
            {title: \'Full width photo\', content: \''.$bigimage.'\'}
            ];';
            }
            if ($template == 1) {
                echo '{title: \'Default Page\', content: \'<div id="header-img" style="display: block; margin-left: auto; margin-right: auto; width:985px; height:65px;"  ></div><p style="text-align: center;">-page-</p><div id="container-normal"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Page with Video\', content: \'<div id="header-img" style="display: block; margin-left: auto; margin-right: auto; width:985px; height:65px;"  ></div><p style="text-align: center;">-page-</p><div id="container-normal"><h1 style="text-align:center;">Title</h1><p><br/>'.$videoelementfamily.'<br/>Edit the video after inserting to use your own video.</p>\'},
            {title: \'Family Video\', content: \'<p>'.$videoelementfamily.'<br/>Edit the video after inserting to use your own video.</p>\'},
            {title: \'Two photos row\', content: \''.$imagetemplatefamily.'\'},
            {title: \'Full width photo\', content: \''.$bigimage.'\'}
            ];';
            }
            if ($template == 2) {
                echo '{title: \'Default Page\', content: \'<div id="header-img" style="display: block; margin-left: auto; margin-right: auto; width:985px; height:65px;"  ></div><p style="text-align: center;">-page-</p><div id="container-normal"><h1 class="max">Title</h1><br/><p>Place here your content.</p></div>\'},
                {title: \'Blue Page\', content: \'<div id="container-max" style="padding-top:40px;"><h1>Title</h1><br/><p>Place here your content.</p><br/><br/><br/></div>\'},
                {title: \'Princess Page\', content: \'<div id="container-princess" style="padding-top:40px;"><h1>Title</h1><br/><p>Place here your content.</p><br/><br/><br/></div>\'},
            {title: \'Video\', content: \'<p>'.$videoelement.'<br/>Edit the video after inserting to use your own video.</p>\'},
            {title: \'Princess title\', content: \''.$princesstitle.'\'},
            {title: \'Full width picture\', content: \''.$childbigimage.'\'}
            ];';
            }
            if ($template == 3) {
                echo '{title: \'Numbered Page\', content: \'<div id="container-normal"><h1 style="text-align:center">Title</h1><p style="text-align:center">~(number)~</p><br/><p>Place here your content.</p></div>\'},
                {title: \'Normal page\', content: \'<div id="container-normal"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Reverse Page\', content: \'<div id="container-reverse"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Castle Page\', content: \'<div id="container-steam"><h1>Title</h1><br/><p>Place here your content.</p></div>\'},
            {title: \'Two photos row\', content: \''.$imagetemplatefamily.'\'},
            {title: \'Full width photo\', content: \''.$bigimage.'\'}
            ];';
            }

            echo '
            var status='.$_SESSION['status'].';';

            echo '
			var linklist = [';
            for ($i = 0; $i < count($chapterTitles); ++$i) {
                echo '{title: "'.$chapterTitles[$i].'", value: "'.$chapterNrs[$i].'-chapter.xhtml'.'"},';
            }

            $uploadpath = preg_replace('/ebooks\//', '', $book['rootUrl'], 1).'OPS/assets/';
            $_SESSION['RF']['subfolder'] = $uploadpath;
            echo '];
			
			loadCreator("'.$uploadpath.'", linklist,templatelist,status,'.$template.'); </script>';
        }

        include 'menu2.php';

        ?>


<div class="container" id="app-container" style="margin-top:50px;">
    <?php if (isset($err)) {
            ?>
    <div class="alert alert-danger alert-dismissable">
        <h4>
            Warning!
        </h4> <span class='glyphicon glyphicon-floppy-remove'></span> <?php echo htmlspecialchars($err); ?>
    </div>
    <?php
        } ?>
    <div id="alerts">
        <?php if (isset($success)) {
            echo '<div class="alert alert-success alert-dismissable">'.
                    '<button type="button" class="close" data-dismiss="alert">'.
                    '&times;</button><span class="glyphicon glyphicon-floppy-saved"></span> '.htmlspecialchars($success).'</div>';
        }?>
    </div>

    <?php
                if ($_SESSION['status'] != 2) {
                    echo '<div class="alert alert-danger alert-dismissable">'.
                    '<button type="button" class="close" data-dismiss="alert">'.
                    '&times;</button><a style="color:#000;" href="http://emrepublishing.com/author-publisher-plans/" target="_blank"><span class="glyphicon glyphicon-upload"></span> Upgrade now and enable file uploads and additional features!</a><br/></div>';
                }
            ?>

    <div class="col-md-12">
        <h2>Book: <?php echo $booktitle; ?>
            <a class="btn btn-default" href="edittitle.php?book=<?php echo $B['id']; ?>"><span
                    class="glyphicon glyphicon-edit"></span> Edit title / cover</a>
        </h2>
        <form role="form" action="" id="create_form" method="post">
            <input type="hidden" name="bookid" value="<?php echo $bookid; ?>">
            <input type="hidden" name="chapterid" value="<?php echo $chapterIds[$currentchapter]; ?>">
            <div class="form-group">
                <label for="Password">Chapter Title</label>
                <input type="text" name="chaptertitle" class="form-control"
                    id="Password" value="<?php echo $chapterTitles[$currentchapter]; ?>">
            </div>

            <textarea name="bookcontent" class="ebook-edit" style="width:100%; height:500px">
                <?php if (isset($chapterContents)) 
                    {
                        echo str_replace('../', $book['rootUrl'].'OPS/', $chapterContents[$currentchapter]);
                    }
                ?>
            </textarea>
            
            <br />
            <button type="submit" name="save" id="savebtn" value="save" class="btn btn-primary"><span
                    class="glyphicon glyphicon-floppy-disk"></span> Save</button>
            <?php echo '<a class="btn btn-default" target="_blank" href="'.$VIEWURL.$book['rootUrl'].'"><span class="glyphicon glyphicon-eye-open"></span> Preview book</a>'; ?>

        </form>
    </div>
</div>
</body>

</html>