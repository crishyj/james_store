<div class="navmenu navmenu-default navmenu-fixed-left offcanvas-sm" style="margin-top:36px; z-index:800">
  <a class="navmenu-brand visible-md visible-lg" href="javascript:void(null);"><?php echo $booktitle; ?></a>
  <?php
  if (isset($B) && $B['coverHref'] != '') {
      echo '<img src="'."$B[coverHref]".'" style="max-width:90%;max-height:300px; margin-left:5%;" alt="book">';
  }
  ?>
  <ul class="nav navmenu-nav">
    <li style="margin-left:10px;"><h3>Chapters</h3></li>
    <?php
    for ($i = 0; $i < count($chapterTitles); ++$i) {
        echo '<li style="width:100%;"> <a style="position:relative; clear:both; float:left; width:80%;" href="?book='.$B['id'].'&cchapt='.$chapterNrs[$i].'">- '.$chapterTitles[$i].'</a>
          <div class="onhoverbuttons" style="clear:both; text-align:right;" >';
        if ($i > 0) {
            echo '<a href="?book='.$bookid.'&chapter_id='.$chapterIds[$i].'&action=up" style="margin-left:3px;" class="btn btn-default"><span class="glyphicon glyphicon-chevron-up"></span></a>';
        }
        if ($i < count($chapterTitles) - 1) {
            echo '<a href="?book='.$bookid.'&chapter_id='.$chapterIds[$i].'&action=down" style="margin-left:3px;" class="btn btn-default"><span class="glyphicon glyphicon-chevron-down"></span></a>';
        }

        echo '      <form role="form"action="" id="delete_form" method="post" style="float:right;margin-left:3px;" onsubmit="return confirm(\'Are you sure you want to delete this chapter? \nThis action cannot be undone.\');">
                    <input type="hidden" name="chapterid" value="'.$chapterIds[$i].'">
                    <input type="hidden" name="bookid" value="'.$bookid.'">
               
                    <button  type="submit" name="delete" id="deletebtn" value="delete" style="" class="btn btn-warning">
                      <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </form>  
          </div>


      </li>';
    }
    ?>
    <li class="divider"></li>
   
    <a style="margin-left:10px;" class="btn btn-default" href="?book=<?php echo $B['id']; ?>&action=newchapter"><span class="glyphicon glyphicon-plus"></span> New chapter</a>
    
	<li class="divider"></li>
  <li><hr> </li>
	
	<?php
    echo '<li style="margin-left:10px;"><h3>Audio Playlist</h3></li>';
    if (count($audioTitles) > 0) {
        for ($i = 0; $i < count($audioTitles); ++$i) {
            echo '
          <li style="width:100%;">
          <a style="position:relative; float:left; width:80%;" href="'.$audioSources[$i].'" target="_blank">
              - '.$audioTitles[$i].'<br/>
          </a>
            <form role="form" style="display:inline-block; float:left;" action="" id="delete_form" method="post" onsubmit="return confirm(\'Are you sure you want to delete this song from the playlist? \nThis action cannot be undone.\');">
              <input type="hidden" name="audioid" value="'.$audioIds[$i].'">
              <input type="hidden" name="bookid" value="'.$bookid.'">          
              <button  type="submit" name="deleteAudio" id="deletebtn" value="delete" style="margin-top:5px;" class="btn btn-default">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>  
          </li>';
        }
    }
    ?>
    
    <div style="margin-left:10px; margin-right:10px;">
      <form action="" method="POST">
         <input type="hidden" name="bookid" value="<?php echo $bookid; ?>">
         <div class="form-group">
             <label for="songtitle">Song Title</label><input type="text" name="songtitle" class="form-control" id="songtitle" required="required">
        </div>
         <div class="form-group" style="width:100%;">
             <label for="songlocation">File location</label><input type="text" name="songlocation" class="form-control" style="width:75%; float:left;" id="songlocation" required="required">
             <a class="btn btn-primary iframe-btn" href="filemanager/dialog.php?type=3&field_id=songlocation" style="height:34px; width:20%; float:left; margin-left:4%;"><span class="glyphicon glyphicon-folder-open"></span></a>
        </div>
        <br/><br/>
        <button type="submit" name="addSong" value="new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add song</button>
      </form>
    </div>

  
	<?php
    echo '<li style="margin-left:10px;"><h3>Video Playlist</h3></li>';
    if (count($videoTitles) > 0) {
        for ($i = 0; $i < count($videoTitles); ++$i) {
            echo '
          <li style="width:100%;">
          <a style="position:relative; float:left; width:80%;" href="'.$videoSources[$i].'" target="_blank">
              - '.$videoTitles[$i].'<br/>
          </a>
            <form role="form" style="display:inline-block; float:left;" action="" id="delete_form" method="post" onsubmit="return confirm(\'Are you sure you want to delete this video from the playlist? \nThis action cannot be undone.\');">
              <input type="hidden" name="videoid" value="'.$videoIds[$i].'">
              <input type="hidden" name="bookid" value="'.$bookid.'">          
              <button  type="submit" name="deleteVideo" id="deletebtn" value="delete" style="margin-top:5px;" class="btn btn-default">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>  
          </li>';
        }
    }
    ?>
    
    <div style="margin-left:10px; margin-right:10px;">
      <form action="" method="POST">
         <input type="hidden" name="bookid" value="<?php echo $bookid; ?>">
         <div class="form-group">
             <label for="videotitle">Video Title</label><input type="text" name="videotitle" class="form-control" id="videotitle" required="required">
        </div>
         <div class="form-group" style="width:100%;">
             <label for="videolocation">File location</label><input type="text" name="videolocation" class="form-control" style="width:75%; float:left;" id="videolocation" required="required">
             <a class="btn btn-primary iframe-btn" href="filemanager/dialog.php?type=3&field_id=videolocation" style="height:34px; width:20%; float:left; margin-left:4%;"><span class="glyphicon glyphicon-folder-open"></span></a>
        </div>
        <br/><br/>
        <button type="submit" name="addVideo" value="new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add Video</button>
      </form>
    </div>

    <?php
    echo '<li style="margin-left:10px;"><h3>Youtube Video Playlist</h3></li>';
    if (count($vimeoTitles) > 0) {
        for ($i = 0; $i < count($vimeoTitles); ++$i) {
            echo '
          <li style="width:100%;">
          <a style="position:relative; float:left; width:80%;" href="'.$vimeoSources[$i].'" target="_blank">
              - '.$vimeoTitles[$i].'<br/>
          </a>
            <form role="form" style="display:inline-block; float:left;" action="" id="delete_form" method="post" onsubmit="return confirm(\'Are you sure you want to delete this video from the playlist? \nThis action cannot be undone.\');">
              <input type="hidden" name="vimeoid" value="'.$vimeoIds[$i].'">
              <input type="hidden" name="bookid" value="'.$bookid.'">          
              <button  type="submit" name="deleteVimeo" id="deletebtn" value="delete" style="margin-top:5px;" class="btn btn-default">
                <span class="glyphicon glyphicon-trash"></span>
              </button>
            </form>  
          </li>';
        }
    }
    ?>

    <div style="margin-left:10px; margin-right:10px;">
      <form action="" method="POST">
         <input type="hidden" name="bookid" value="<?php echo $bookid; ?>">
         <div class="form-group">
             <label for="vimeotitle">Youtube Video Title</label>
             <input type="text" name="vimeotitle" class="form-control" id="videotitle" required="required">
          </div>
          <div class="form-group">
             <label for="vimeoLink">Youtube Video Link</label>
             <input type="text" name="vimeoLink" class="form-control" id="vimeoLink" required="required">
          </div>
        <div class="form-group">
            <button type="submit" name="addVimeo" value="new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add Video</button>
        </div>
      </form>
    </div>
  </ul>
</div>


<div class="navbar navbar-default navbar-fixed-top">
    <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".navmenu" data-canvas="body">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
  </button>
 
            <!-- <button type="button" class="btn btn-default icon icon-show-hide"></button>  -->
    <button id="aboutButt1" tabindex="1" style="width:auto;" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
        <div class="icon-readium"> </div> Epub Creator Studio
    </button>
        
       
    
    <div class="btn-group navbar-right">
        
        <a href="home.php" tabindex="1" type="button" class="btn icon-book" title="My books" aria-label="users" style="width:auto;">
            <span class="glyphicon glyphicon-book" aria-hidden="true"></span> My books
        </a>
        
        <a href="logout.php" tabindex="1" type="button" class="btn icon-logout" title="logout" aria-label="logout" style="width:auto;">
            <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout
        </a>

       
        <!-- <button type="button" class="btn btn-default icon icon-bookmark"></button>-->
    </div>
</div>
