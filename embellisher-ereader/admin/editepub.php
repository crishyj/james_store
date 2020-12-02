<?php
        require 'header.php';

        if (isset($_GET['id'])) {
            $id = $DB->real_escape_string($_GET['id']);
            $SQL = "SELECT * FROM library WHERE id='$id'";
            $RES = $DB->query($SQL);
            $epub = $RES->fetch_assoc();
        } else {
            exit();
        }

        include 'menu.php';
        ?>
<div id="app-container">
    <?php if (isset($err)) {
            ?>
    <div class="alert alert-danger alert-dismissable">

        <h4>
            Error!
        </h4> <?php echo $err; ?>
    </div>
    <?php
        } ?>
    <?php if (isset($success)) {
            ?>
    <div class="alert alert-success alert-dismissable">
        <h4>
            Success!
        </h4> <?php echo $success; ?>
    </div>
    <?php
        } ?>
    <div class="jumbotron">
        <div class="container">
            <h1 style="font-size:28px;">Change store item</h1>

        </div>
    </div>

    <div class="col-md-12">
        <div id="tab-user-register" class="tab-pane active">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Change item</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="storeitems.php" enctype="multipart/form-data" method="POST"
                        onsubmit="return confirm('Are you sure?');">

                        <input type="hidden" class="form-control" id="epubid" value="<?php echo $epub['id']; ?>"
                            name="epubid" required="required">

                        <div class="form-group">
                            <label for="register-name">Title:</label>
                            <input type="text" class="form-control" id="register-name"
                                value="<?php echo htmlspecialchars($epub['title']); ?>" name="title" required="required">
                        </div>
                        <div class="form-group">
                            <label for="register-email">Author:</label>
                            <input type="text" class="form-control" id="register-email" name="author"
                                value="<?php echo htmlspecialchars($epub['author']); ?>" required="required">
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre:</label>
                            <select class="form-control" name="genre" id="genre" required="required">
                                <?php
                                    $new_genres = $_SESSION['genres'];

                                    if($_SESSION['genres']){
                                    $genres = explode(',', $new_genres);
                                    array_unshift($genres, '');
                                    }
                                    foreach ($genres as $key => $value) {
                                        echo '<option value="'.$value.'"';
                                        if ($epub['genre'] == $value) {
                                            echo ' selected';
                                        }
                                        echo '>'.$value.'</option>';
                                    }123
                                    ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="register-price">Price:</label>
                            <input type="text" class="form-control" id="register-price" name="price"
                                value="<?php echo htmlspecialchars($epub['price']); ?>" required="required">
                        </div>
                        <div class="form-group">
                            <label for="Description">Description:</label>
                            <textarea class="form-control" name="description" id="Description"
                                required="required"><?php echo htmlspecialchars($epub['description']); ?> </textarea>
                        </div>
                        <div class="form-group">
                            <label for="Excerpt">Excerpt:</label>
                            <textarea class="form-control" name="excerpt"
                                id="Excerpt"><?php echo htmlspecialchars($epub['excerpt']); ?> </textarea>
                        </div>

                        <div class="form-group">
                            <label for="preload">Preload book for new users (book is added to library of users when they
                                register):</label>
                            <select class="form-control" name="preload" id="preload" required="required">
                                <option value="0" <?php if ($epub['preload'] == 0) {
                                        echo 'selected';
                                    } ?>>Do not preload
                                </option>
                                <option value="1" <?php if ($epub['preload'] == 1) {
                                        echo 'selected';
                                    } ?>>Preload
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="epubfile">Epub file (only add if you want to change the epub itself!):</label>
                            <input type="file" title="Browse epub zip file" name="epub">
                        </div>

                        <button type="submit" name="changeepub" id="change" tabindex="1000" class="btn btn-primary"
                            title="Save" aria-label="Save">Save item</button>
                        <button type="submit" name="changeepub" value="delete" id="delete" tabindex="1000"
                            class="btn btn-danger" title="Delete" aria-label="Delete">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>