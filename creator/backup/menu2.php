<div class="navmenu navmenu-default navmenu-fixed-left offcanvas-sm" style="margin-top:36px; z-index:800">
  <a class="navmenu-brand visible-md visible-lg" href="javascript:void(null);"><?php echo $booktitle; ?></a>
  <?php 
  if (isset($B) && $B['coverHref']!=""){
    echo '<img src="'.$B[coverHref].'" style="max-width:90%;max-height:300px; margin-left:5%;" alt="book">';
  }
  ?>
  <ul class="nav navmenu-nav">
    <li style="margin-left:10px;"><h3>Chapters</h3></li>
    <?php
    for($i = 0; $i < count($chapterTitles); ++$i) {
      echo '<li style="width:100%;"> <a style="position:relative; float:left; width:80%;" href="?book='.$B['id'].'&cchapt='.$chapterNrs[$i].'">- '.$chapterTitles[$i].'</a>

                <form role="form" style="display:inline-block; float:left;" action="" id="delete_form" method="post" onsubmit="return confirm(\'Are you sure you want to delete this chapter? \nThis action cannot be undone.\');">
                    <input type="hidden" name="chapterid" value="'.$chapterIds[$currentchapter].'">
                    <input type="hidden" name="bookid" value="'.$bookid.'">
               
                    <button  type="submit" name="delete" id="deletebtn" value="delete" class="btn btn-default">
                      <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </form>  


      </li>';
    }
    ?>
    <li class="divider"></li>
    <i>
      <a style="margin-left:10px;" class="btn btn-default" href="?book=<?php echo $B['id']; ?>&action=newchapter">New chapter</a>
    </li>
    <!--<li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Actions.. <b class="caret"></b></a>
      <ul class="dropdown-menu navmenu-nav">
        <li class="dropdown-header">Add..</li>
        <li></li>
        
      </ul>
    </li>-->
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
