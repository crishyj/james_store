<?php
      require 'header.php';
      if ($_SESSION['type'] != 1) {
          exit();
      }

      if (isset($_GET['id'])) {
          $id = $DB->real_escape_string($_GET['id']);
          $SQL = "SELECT * FROM user WHERE id='$id'";
          $RES = $DB->query($SQL);
          $user = $RES->fetch_assoc();
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
            Warning!
        </h4> <?php echo htmlspecialchars($err); ?>
    </div>
    <?php
} ?>
    <?php if (isset($success)) {
        ?>
    <div class="alert alert-success alert-dismissable">
        <h4>
            Success!
        </h4> <?php echo htmlspecialchars($success); ?>
    </div>
    <?php
    } ?>
    <div class="jumbotron">
        <div class="container">
            <h1 style="font-size:28px;">Change user account</h1>
            <p>
            <a href="home.php?id=<?php echo $user['id']; ?>&type=<?php echo $user['admin']; ?>&name=<?php echo $user['name']; ?>">
              Log in as this user
            </a></p>
        </div>
    </div>

    <div class="col-md-12">
        <div id="tab-user-register" class="tab-pane active">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Change user</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="users.php" method="POST">

                        <input type="hidden" class="form-control" id="userid" value="<?php echo $user['id']; ?>"
                            name="userid" required="required">

                        <div class="form-group">
                            <label for="register-name">Name:</label>
                            <input type="text" class="form-control" id="register-name"
                                value="<?php echo htmlspecialchars($user['name']); ?>" name="name" required="required">
                        </div>
                        <div class="form-group">
                            <label for="register-email">E-mail:</label>
                            <input type="email" class="form-control" id="register-email" name="username"
                                value="<?php echo htmlspecialchars($user['email']); ?>" required="required">
                        </div>
                        <div class="form-group">
                            <label for="register-password">Password:</label>
                            <input type="password" class="form-control" id="register-password" name="password"
                                placeholder="Leave empty to keep the same password" value="">
                        </div>
                        <div class="form-group">
                            <label for="register-maxsessions">Maximum sessions:</label>
                            <input type="number" class="form-control" id="register-maxsessions" name="maxsessions"
                                value="<?php echo $user['maxsessions']; ?>" required="required">
                        </div>

                        <div class="form-group">
                            <label for="registertype">Select type:</label>
                            <select class="form-control" name="type" id="registertype">
                                <option value="Reader" <?php if ($user['type'] == 'Reader') {
                                        echo 'selected';
                                    }?>>Reader
                                </option>
                                <option value="Author" <?php if ($user['type'] == 'Author') {
                                        echo 'selected';
                                    }?>>Author
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="registeradmin">Select account type:</label>
                            <select class="form-control" name="admin" id="registeradmin">
                                <option value="-1" <?php if ($user['admin'] == '-1') {
                                        echo 'selected';
                                    }?>>Read only user
                                </option>
                                <option value="0" <?php if ($user['admin'] == '0') {
                                        echo 'selected';
                                    }?>>Publisher
                                </option>
                                <option value="1" <?php if ($user['admin'] == '1') {
                                        echo 'selected';
                                    }?>>Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Select Creator Studio type:</label>
                            <select class="form-control" name="status" id="status">
                                <option value="1" <?php if ($user['status'] != '2') {
                                        echo 'selected';
                                    }?>>Standard Access
                                </option>
                                <option value="2" <?php if ($user['status'] == '2') {
                                        echo 'selected';
                                    }?>>Full Access
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="allfree">All Books Free:</label>
                            <select class="form-control" name="allfree" id="allfree">
                                <option value="0" <?php if ($user['allfree'] == 0) {
                                        echo 'selected';
                                    }?>>No</option>
                                <option value="1" <?php if ($user['allfree'] == 1) {
                                        echo 'selected';
                                    }?>>Yes</option>
                            </select>
                        </div>
                        <button type="submit" name="changeuser" id="buttRegisterProfile" tabindex="1000"
                            class="btn btn-primary" title="Sign up" aria-label="Sign up">Save account</button>
                        <button type="submit" name="changeuser" value="delete" id="delete" tabindex="1000"
                            class="btn btn-danger" title="Delete" aria-label="Delete">Delete account</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>