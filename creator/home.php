<?php
        require 'header.php';
        require_once 'includes/functions.php';

        if (isset($_POST['add_to_store']) && isset($_POST['bookid'])) {
            $userid = $_SESSION['user_id'];
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $price = $DB->real_escape_string($_POST['amount']);
            $SEL = "SELECT * FROM private_library WHERE id='$bookid' AND userid='$userid'";
            $res = $DB->query($SEL);
            if ($B = $res->fetch_assoc()) {
                $title = $DB->real_escape_string($B['title']);
                $author = $DB->real_escape_string($B['author']);
                $owner = $userid;
                $coverHref = $DB->real_escape_string($B['coverHref']);
                $packagePath = $DB->real_escape_string($B['packagePath']);
                $rootUrl = $DB->real_escape_string($SERVERURL.$B['rootUrl']);
                $INSERTBOOK = "INSERT INTO `library` (`title`,`author`,`coverHref`,`packagePath`,`rootUrl`,`price`,`owner`) VALUES ('$title','$author','$coverHref','$packagePath','$rootUrl','$price','$owner')";
                if (!$DB->query($INSERTBOOK)) {
                    $err = 'Could not insert book into the store.'.$DB->error;
                }
                else{
                    $GETEPUB = "SELECT * FROM library WHERE owner = '$owner' && rootUrl = '$rootUrl'";
                    $RES = $DB->query($GETEPUB);
                    while ($epub = $RES->fetch_assoc()) {
                        $library_id = $epub['id'];
                    }
                    $INSERTULIB = "INSERT INTO user_library (userid, libraryid) VALUES('$owner', '$library_id')";
                    if($DB->query($INSERTULIB)){
                        $success = 'Succesfully added epub!';
                    }
                }
            } else {
                $err = 'Could not insert book into the store.';
            }
        }

        if (isset($_POST['delete']) && isset($_POST['bookid'])) {
            $userid = $_SESSION['user_id'];
            $bookid = $DB->real_escape_string($_POST['bookid']);
            $SEL = "SELECT * FROM private_library WHERE id='$bookid' AND userid='$userid'";
            $res = $DB->query($SEL);
            if ($B = $res->fetch_assoc()) {
                $loc = $B['rootUrl'];
                rmdir_recursive($loc);
                $DEL = "DELETE FROM private_chapters WHERE bookid='$bookid'";
                $res = $DB->query($DEL);
                $DEL = "DELETE FROM private_library WHERE id='$bookid'";
                $res = $DB->query($DEL);
            } else {
                $err = 'Could not delete ebook, this book does not exist or does not belong to you.';
            }
        }

        include 'menu.php';
        ?>
        <div id="app-container">
            <?php if (isset($err)) {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <h4>
                    Warning!
                </h4> <?php echo htmlspecialchars($err); ?>
            </div>
            <?php
        } ?>
            <div class="container">
                <div class="jumbotron">
                    <h2>Welcome to the ePub Creator Studio</h2>
                    <p>Create and manage your ePub3 books here.</p>
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
                             <label for="lang">Language (2 letter code)</label><input type="text" name="lang" class="form-control" id="author" required="required" value="en">
                        </div>
                        <div class="form-group">
                            <label for="pagination">Pagination direction</label>
                            <select id="pagination" name="pagination">
                                <option value="ltr" selected="selected">Left to right</option>
                                <option value="rtl">Right to left</option>      
                            </select>
                        </div>

                        <div class="form-group">
                             <label for="coverimage">Cover Image (optional)</label><input type="file" title="Browse cover image" name="coverimage" accept="image/*">
                             <p>* Only png, jpg or gif are allowed. Recommended size of 1024px x 800px.</p>
                        </div>
                        <div class="form-group">
                            <label for="template">Template</label>
                            <input type="hidden" name="template" id="hiddentemplate" value="0">
                            <select id="template" class="ddslick">
                                <option value="0" data-imagesrc="images/templates/0.png"
                                    data-description="Start from scratch.">Empty</option>
                                     <?php

                                        if ($_SESSION['status'] == 2) {
                                            ?>
                                            <option value="1" data-imagesrc="images/templates/1.png"
                                                data-description="Share photos, videos and stories with your family">Family Love</option>
                                            <option value="2" data-imagesrc="images/templates/2.jpg"
                                                data-description="Children books">Children</option>
                                            <option value="3" data-imagesrc="images/templates/3.jpg"
                                                data-description="Medieval books">Medieval</option>
                                        <?php
                                        }
                                    ?>
                            </select>
                            <p>* You cannot modify the template later.</p>
                        </div>

                        <?php

                            if ($_SESSION['status'] != 2) {
                                echo '<a href="http://emrepublishing.com/author-publisher-plans/" target="_blank" class="btn btn-warning">Upgrade now!</a><br/>
                                <p>*Upgrade to full access and get additional templates and features.</p>';
                            }
                        ?>

                        <button type="submit" name="create" value="new" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Create new Ebook</button>
                    </form> 
                </div>
                <div class="col-md-8 list-view">


                    <h2>My Ebooks</h2>
                    <hr>
                    <?php
                    $userid = $_SESSION['user_id'];
                    $SEL = "SELECT * FROM private_library WHERE userid='$userid'";
                    $rb = $DB->query($SEL);
                    while ($B = $rb->fetch_assoc()) {
                        $sharelink = $VIEWURL.$B['rootUrl'];
                        $viewlink = $VIEWURL.$B['rootUrl'];
                        if ($_SESSION['type'] == 1) {
                            $root = $SERVERURL.$B['rootUrl'];
                            $CHECK_STORE = "SELECT * FROM library WHERE rootUrl='$root'";
                            $book_store = $DB->query($CHECK_STORE);
                            if ($storebook = $book_store->fetch_assoc()) {
                                $sharelink = $STOREURL.$storebook['id'];
                            }
                        }

                        echo '<div class="col-xs-4 col-sm-3 col-md-2 library-item">
                                <div class="info-wrap">
                                    <a href="creator.php?book='.$B['id'].'" type="button" style="background: none; border: none; padding: 0; margin: 0;" >';
                        if ($B['coverHref'] != '') {
                            echo '<img aria-hidden="true" style="width:120px;" class="img-responsive" src="'.$B['coverHref'].'" alt="" /> ';
                        } else {
                            echo '<div aria-hidden="true"  class="no-cover" style="background-image: url(\'images/covers/cover1.jpg\'); height:140px; width:120px;" >'.$B['title'].'</div> ';
                        }
                        echo '
                                    </a>
                                </div>
                               
                                <div class="caption book-info">
                                    <h4 class="title">'.htmlspecialchars($B['title']).'</h4>
                                    <div class="author">'.htmlspecialchars($B['author']).'</div>
                                    <div class="buttons">

                                      <a class="btn btn-primary" href="creator.php?book='.$B['id'].'"><span class="glyphicon glyphicon-edit"></span> Edit book</a>  
                                      <form style="display:inline-block;" role="form" action="" id="delete_form" method="post" onsubmit="return confirm(\'Are you sure you want to delete this book? \nThis action cannot be undone.\')">
                                            <input type="hidden" name="bookid" value="'.$B['id'].'">
                                       
                                            <button type="submit" name="delete" id="deletebtn" value="delete" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                                        </form> 
                                      <br/>
									  <input type="text"  style=" width:220px;" class="form-control" value="'.$sharelink.'"/>
                                       <a class="btn btn-default" target="_blank" href="'.$viewlink.'"><span class="glyphicon glyphicon-eye-open"></span> View book</a> 
                                      <!-- Button trigger modal -->
                                        <a type="button" class="btn btn-default" data-toggle="modal" data-target="#downloadmodal'.$B['id'].'">
                                          <span class="glyphicon glyphicon-download-alt"></span> Download
                                        </a>

                                        <!-- Modal -->
                                        <div class="modal fade" id="downloadmodal'.$B['id'].'" tabindex="-1" role="dialog" aria-labelledby="Download" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h2 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-download-alt"></span> Download eBook</h2>
                                              </div>
                                              <div class="modal-body">
                                                <p>You can download your ebook in two different formats.</p>
                                                <p>
                                                <a class="btn btn-primary" target="_blank" href="download.php?book='.$B['id'].'">Download ePub</a> 
                                                <!--<a class="btn btn-default" target="_blank" href="downloadmobi.php?book='.$B['id'].'">Download Mobi</a> 
                                                <a class="btn btn-default" target="_blank" href="downloadpdf.php?book='.$B['id'].'">Download PDF</a>-->
                                                <a class="btn btn-default" target="_blank" href="downloadapple.php?book='.$B['id'].'">Download Apple ePub</a>
                                                <br/><br/>
                                                For additional formats you can use an online convertion service such as <a href="http://toepub.com" target="_blank">ToePub</a>.

                                                <!--<b>EPub3</b>, which is recommeneded and supports all functionality offered by the ePub Creator Studio.<br/>
                                                <b>Mobi</b>, which has limited support for images and videos, no support for javascript and custom css.<br/>
                                                <b>PDF</b>, which has no support for videos and javascript.<br/>-->
                                                </p>
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                        <a href="https://twitter.com/share" class="twitter-share-button" data-url="https://emrepublishing.com/creator/twittercard.php?title='.urlencode($B['title']).'&coverHref='.urlencode($B['coverHref']).'&link='.$sharelink.'" >Tweet</a>
                                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>

										<br/>';
                        if ($_SESSION['type'] == 1) {
                            $root = $SERVERURL.$B['rootUrl'];
                            $CHECK_STORE = "SELECT * FROM library WHERE rootUrl='$root'";
                            $book_store = $DB->query($CHECK_STORE);
                            if (!$book_store->fetch_assoc()) {
                                //book is not linked in store yet.
                                echo '<br/><form style="display:inline-block;" role="form" action="" id="store_form" method="post" onsubmit="return confirm(\'Are you sure you want to add this book to the store? \')">
                                            <input type="hidden" name="bookid" value="'.$B['id'].'">
											<input type="text" class="form-control" name="amount" value="" placeholder="Book price" style="display:inline-block; width:50%;" >
                                            <button type="submit" name="add_to_store" id="add_to_store" value="add_to_store" class="btn btn-primary"><span class="glyphicon glyphicon-transfer"></span> Add to Store</button>
                                        </form>  ';
                            } else {
                                echo '<a class="btn btn-default" target="_blank" href="/embellisher-ereader/admin/"><span class="glyphicon glyphicon-shopping-cart"></span> View in store</a> 
									  ';
                            }
                        }
                        echo '
                                    </div>
                                </div>
                            </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
    <script>
    loadhome();
    </script>

</html>
