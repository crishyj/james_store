<?php
        ini_set('max_execution_time', 60000);
        require 'header.php';
              
        $err = '';
        function rmdir_recursive($dir)
        {
            foreach (scandir($dir) as $file) {
                if ('.' === $file || '..' === $file) {
                    continue;
                }
                if (is_dir("$dir/$file")) {
                    rmdir_recursive("$dir/$file");
                } else {
                    unlink("$dir/$file");
                }
            }
            rmdir($dir);
        }

        if (isset($_POST['changeepub']) && $_POST['epubid'] != '') {
            if (!isset($_POST['title'])) {
                $err = 'Please fill in a title.';
            }
            if (!isset($_POST['author'])) {
                $err = 'Please fill in the author.';
            }
            if (!isset($_POST['genre'])) {
                $err = 'Please fill in a genre.';
            }
            if (!isset($_POST['price'])) {
                $err = 'Please fill in a price.';
            }

            $epublocationchange = '';
            if ($_FILES['epub']['name']) {
                $filename = $_FILES['epub']['name'];
                $source = $_FILES['epub']['tmp_name'];
                $type = $_FILES['epub']['type'];

                $name = explode('.', $filename);
                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                foreach ($accepted_types as $mime_type) {
                    if ($mime_type == $type) {
                        $okay = true;
                        break;
                    }
                }
                $continue = false;
                if (strtolower($name[count($name) - 1]) == 'zip' || strtolower($name[count($name) - 1]) == 'epub') {
                    $continue = true;
                }

                if (!$continue) {
                    $err = 'The epub file you are trying to upload is not a .zip or epub file. Please try again.';
                } else {
                    $path = dirname(__FILE__); // absolute path to the directory where zipper.php is in
                    $filenoext = basename($filename, '.zip'); // absolute path to the directory where zipper.php is in (lowercase)
                    $filenoext = basename($filenoext, '.ZIP');
                    $filenoext = basename($filenoext, '.epub');
                    $filenoext = basename($filenoext, '.EPUB');
                    $targetdir = 'epubs/'.$filenoext; // target directory
                    $targetzip = 'epubs/'.$filenoext.'.zip'; // target zip file
                    $originaltarget = $targetdir;
                    $duplicatecounter = 0;
                    while (is_dir($targetdir)) {
                        $targetdir = $originaltarget.$duplicatecounter;
                        ++$duplicatecounter;
                    }
                    mkdir($targetdir, 0755);

                    if (move_uploaded_file($source, $targetzip)) {
                        $zip = new ZipArchive();
                        $x = $zip->open($targetzip); // open the zip file to extract
                        if ($x === true) {
                            $zip->extractTo($targetdir); // place in the directory with same name
                            $zip->close();
                            unlink($targetzip);
                        } else {
                            $err = 'Could not process zip or epub file.';
                        }
                        if (!file_exists($targetdir.'/META-INF/container.xml')) {
                            $err = 'EPUB ERROR: Could not locate META-INF container.xml!';
                        } else {
                            $containerinfo = new DOMDocument();
                            $containerinfo->load($targetdir.'/META-INF/container.xml');
                            $pathinfo = $containerinfo->getElementsByTagName('rootfile')->item(0)->getAttribute('full-path');
                            $pathinfo = $DB->real_escape_string($pathinfo);
                        }
                        $message = 'Your zip or epub file was uploaded and unpacked.';
                        $epublocation = $SERVERURL.$targetdir;
                        $epublocationchange = ", rootUrl='".$DB->real_escape_string($epublocation)."', packagePath = '".$pathinfo."'";
                    } else {
                        $err = 'There was a problem with the epub upload. Please try again.';
                    }
                }
            }

            $epubid = $DB->real_escape_string($_POST['epubid']);
            $title = $DB->real_escape_string($_POST['title']);
            $preload = $DB->real_escape_string($_POST['preload']);
            $author = $DB->real_escape_string($_POST['author']);
            $genre = $DB->real_escape_string($_POST['genre']);
            $price = $DB->real_escape_string($_POST['price']);
            $description = $DB->real_escape_string($_POST['description']);
            $excerpt = $DB->real_escape_string($_POST['excerpt']);
            if (!$err && $epubid != '') {
                if ($_POST['changeepub'] == 'delete') {
                    $DELETESQL = "DELETE FROM library WHERE id='$epubid'";
                    if ($DB->query($DELETESQL)) {
                        $success = 'Succesfully deleted epub!';
                    } else {
                        $err = 'Query failed, with error: '.$DB->error;
                    }
                } else {
                    $UPDATESQL = "UPDATE library SET preload='$preload', title='$title',author='$author',genre='$genre',price='$price', description='$description', excerpt='$excerpt' $epublocationchange WHERE id='$epubid'";
                    if ($DB->query($UPDATESQL)) {
                        $success = 'Succesfully changed epub!';
                    } else {
                        $err = 'Query failed, with error: '.$DB->error;
                    }
                }
            }
        }

        if (isset($_POST['addepub']) && $_POST['addepub'] != '') {
            if (!isset($_POST['title'])) {
                $err = 'Please fill in a title.';
            }
            if (!isset($_POST['author'])) {
                $err = 'Please fill in the author.';
            }
            if (!isset($_POST['genre'])) {
                $err = 'Please fill in a genre.';
            }
            if (!isset($_POST['price'])) {
                $err = 'Please fill in a price.';
            }
            if (!$_FILES['epub']['name']) {
                $err = 'Please upload the epub zip file.';
            }
            if (!$_FILES['coverimage']['name']) {
                $err = 'Please upload the cover image.';
            }

            $preload = $DB->real_escape_string($_POST['preload']);
            $title = $DB->real_escape_string($_POST['title']);
            $author = $DB->real_escape_string($_POST['author']);
            $genre = $DB->real_escape_string($_POST['genre']);
            $price = $DB->real_escape_string($_POST['price']);
            $description = $DB->real_escape_string($_POST['description']);
            $excerpt = $DB->real_escape_string($_POST['excerpt']);

            if ($_FILES['epub']['name']) {
                $filename = $_FILES['epub']['name'];
                $source = $_FILES['epub']['tmp_name'];
                $type = $_FILES['epub']['type'];

                $name = explode('.', $filename);
                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                foreach ($accepted_types as $mime_type) {
                    if ($mime_type == $type) {
                        $okay = true;
                        break;
                    }
                }

                if (strtolower($name[count($name) - 1]) == 'zip' || strtolower($name[count($name) - 1]) == 'epub') {
                    $continue = true;
                }
                if (!$continue) {
                    $err = 'The epub file you are trying to upload is not a .zip file. Please try again.';
                } else {
                    $path = dirname(__FILE__); // absolute path to the directory where zipper.php is in
                    $filenoext = basename($filename, '.zip'); // absolute path to the directory where zipper.php is in (lowercase)
                    $filenoext = basename($filenoext, '.ZIP'); // absolute path to the directory where zipper.php is in (when uppercase)
                    $filenoext = basename($filenoext, '.epub');
                    $filenoext = basename($filenoext, '.EPUB');

                    $targetdir = 'epubs/'.$filenoext; // target directory
                    $targetzip = 'epubs/'.$filenoext.'.zip'; // target zip file

                    /* create directory if not exists', otherwise overwrite */
                    /* target directory is same as filename without extension */
                    $originaltarget = $targetdir;
                    $duplicatecounter = 0;
                    while (is_dir($targetdir)) {
                        $targetdir = $originaltarget.$duplicatecounter;
                        ++$duplicatecounter;
                    } //rmdir_recursive ( $targetdir);

                    mkdir($targetdir, 0755);

                    /* here it is really happening */

                    if (move_uploaded_file($source, $targetzip)) {
                        $zip = new ZipArchive();
                        $x = $zip->open($targetzip); // open the zip file to extract
                        if ($x === true) {
                            $zip->extractTo($targetdir); // place in the directory with same name
                            $zip->close();

                            unlink($targetzip);
                        }
                        //Check for META-INF/container.xml
                        if (!file_exists($targetdir.'/META-INF/container.xml')) {
                            $err = 'EPUB ERROR: Could not locate META-INF container.xml!';
                        } else {
                            $containerinfo = new DOMDocument();
                            $containerinfo->load($targetdir.'/META-INF/container.xml');
                            $pathinfo = $containerinfo->getElementsByTagName('rootfile')->item(0)->getAttribute('full-path');
                            $pathinfo = $DB->real_escape_string($pathinfo);
                        }
                        $message = 'Your ebook file was uploaded and unpacked.';
                        $epublocation = $SERVERURL.$targetdir;
                    } else {
                        $err = 'There was a problem with the epub upload. Please try again.';
                    }
                }
            }//add epub

            if ($_FILES['coverimage']['name']) {
                $filename = $_FILES['coverimage']['name'];
                $filename = str_replace(' ', '_', $filename);
                $source = $_FILES['coverimage']['tmp_name'];
                $type = $_FILES['coverimage']['type'];

                $info = getimagesize($_FILES['coverimage']['tmp_name']);
                $okay = true;
                if ($info === false) {
                    $okay = false;
                }

                // $continue = strtolower($name[1]) == 'zip' ? true : false;
                if (!$okay) {
                    $err = 'The cover image file you are trying to upload is not an image. Please try again.';
                } else {
                    /* PHP current path */
                    $path = dirname(__FILE__); // absolute path to the directory where we are
                    $duplicatecounter = 0;
                    $targetdir = 'coverimages/'.$duplicatecounter;
                    $originaltarget = 'coverimages/';

                    while (is_dir($targetdir)) {
                        $targetdir = $originaltarget.$duplicatecounter;
                        ++$duplicatecounter;
                    } //rmdir_recursive ( $targetdir);

                    mkdir($targetdir, 0755);

                    /* here it is really happening */

                    if (move_uploaded_file($source, $targetdir.'/'.$filename)) {
                        $coverlocation = $SERVERURL.$targetdir.'/'.$filename;
                        $message = 'Your .zip file was uploaded and unpacked.';
                    } else {
                        $err = 'There was a problem with the upload of the coverimage. Please try again.';
                    }
                }
            }//add epub

            if (!$err || $err == '') {
                $user_id = $_SESSION['user_id'];
                //finally ready to add epub to database
                $INSERTEPUB = "INSERT INTO library (title,author,coverHref,packagePath,rootUrl,price,description,genre,preload,excerpt,owner) VALUES('$title','$author','$coverlocation','$pathinfo','$epublocation','$price','$description','$genre','$preload','$excerpt','$user_id')";
                //$DB->query($INSERTEPUB);
                if ($DB->query($INSERTEPUB)) {
                    $GETEPUB = "SELECT * FROM library WHERE owner = '$user_id' && rootUrl = '$epublocation'";
                    $RES = $DB->query($GETEPUB);
                    while ($epub = $RES->fetch_assoc()) {
                        $library_id = $epub['id'];
                    }
                    $INSERTULIB = "INSERT INTO user_library (userid, libraryid) VALUES('$user_id', '$library_id')";
                    if($DB->query($INSERTULIB)){
                        $success = 'Succesfully added epub!';
                    }
                    
                } else {
                    $err = 'Query failed, with error: '.$DB->error;
                }
            }
        }

        include 'menu.php';
        ?>
<div id="app-container">
    <?php if (isset($err)) {
            ?>
    <!-- <div class="alert alert-danger alert-dismissable">
        <h4>
            Error!
        </h4>
         <?php echo htmlspecialchars($err); ?>
    </div> -->
    <?php
        } ?>
    <?php if (isset($success)) {
            ?>
    <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
    <div class="alert alert-success alert-dismissable">

        <h4>
            Success!
        </h4> <?php echo htmlspecialchars($success); ?>
    </div>
    <?php
        } ?>
    <div class="jumbotron">
        <div class="container">
            <h1 style="font-size:28px;">Store management</h1>
            <p>Publish your ebooks to the Embellisher Ereader.<br />
        </div>
    </div>

    <?php
      $new_genres = $_SESSION['genres'];

      if($_SESSION['genres']){
        $genres = explode(',', $new_genres);
        array_unshift($genres, '');
      }
      else{
        $genres = array('', 'Drama', 'Fable', 'Fairy Tale', 'Fantasy', 'Fiction', 'Fiction in Verse', 'Folklore', 'Historical Fiction', 'Horror', 'Humor', 'Legend', 'Mystery', 'Mythology', 'Poetry', 'Realistic Fiction', 'Science Fiction', 'Short Story', 'Steampunk', 'Tall Tale', 'Biography/Autobiography', 'Essay', 'Narrative Nonfiction', 'Nonfiction', 'Speech');
      }
      
    ?>

    <div class="col-md-4">
        <div id="tab-user-register" class="tab-pane active">


            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Add epub</h3>

                </div>

                <div class="panel-body">

                    <br />
                    <form role="form" action="" enctype="multipart/form-data" method="post">



                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" id="title" value="" name="title"
                                required="required">
                        </div>
                        <div class="form-group">
                            <label for="author">Author:</label>
                            <input type="text" class="form-control" id="author" name="author" value=""
                                required="required">
                        </div>
                        <div class="form-group">
                            <label for="Description">Description:</label>
                            <textarea class="form-control" name="description" id="Description"
                                required="required"> </textarea>
                        </div>
                        <div class="form-group">
                            <label for="excerpt">Excerpt:</label>
                            <textarea class="form-control" name="excerpt" id="excerpt"> </textarea>
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre or Category:</label>
                            <select class="form-control" name="genre" id="genre" required="required">
                                <?php
                                        foreach ($genres as $key => $value) {
                                            echo '<option value="'.$value.'">'.$value.'</option>';
                                        }
                                    ?>

                            </select>

                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="text" class="form-control" id="price" name="price" value=""
                                required="required">
                            <p>* The final price (after promo codes) has to be at least $ 0.50 (or free).</p>
                        </div>

                        <div class="form-group">
                            <label for="epubfile">Epub file:</label>
                            <input type="file" title="Browse epub zip file" name="epub" required="required">
                            <p>* .epub or .zip</p>
                            <?php
                                 if ($err && $err != '') {
                                     echo '<p class="alert alert-danger">'.$err.'</p>';
                                 }

                                 ?>
                        </div>
                        <div class="form-group">
                            <label for="coverimage">Cover image:</label>
                            <input type="file" title="Browse cover image" name="coverimage" required="required"
                                accept="image/*">
                            <p>* jpg, png or gif</p>
                        </div>

                        <div class="form-group">
                            <label for="preload">Preload book for new users (book is added to library of users when they
                                register):</label>
                            <select class="form-control" name="preload" id="preload" required="required">
                                <option value="0">Do not preload</option>
                                <option value="1">Preload</option>

                            </select>

                        </div>



                        <button type="submit" name="addepub" value="add" id="addEpub" tabindex="1000"
                            class="btn btn-primary" title="Add" aria-label="Add">Add epub</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="tab-storeitems" class="tab-pane active">

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Modify store items</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Genre</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $user_id = $_SESSION['user_id'];
                                $SQL = "SELECT * FROM library WHERE owner = '$user_id' ORDER BY title";
                                $RES = $DB->query($SQL);
                                while ($epub = $RES->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td><img src="'.$epub['coverHref'].'" alt="covericon" style="max-height:50px;"></td> ';
                                    echo '<td>'.htmlspecialchars($epub['title']).'</td> ';
                                    echo '<td>'.htmlspecialchars($epub['author']).'</td> ';
                                    echo '<td>'.htmlspecialchars($epub['genre']).'</td> ';
                                    echo '<td>'.htmlspecialchars($epub['price']).'</td> ';
                                    echo '<td><a href="editepub.php?id='.$epub['id'].'" class="btn btn-warning">Edit</a></td>';
                                    echo '</tr> ';
                                }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>


</div>
</body>

</html>