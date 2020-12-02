<?php
        require 'header.php';
        if (isset($_POST['addcode'])) {
            if (!isset($_POST['code'])) {
                $err = 'Please fill in a coupon code.';
            }
            if (!isset($_POST['bookid'])) {
                $err = 'Please link the code to a book.';
            }

            $user_id = $_SESSION['user_id'];

            $code = $DB->real_escape_string($_POST['code']);
            $free = $DB->real_escape_string($_POST['free']);
            $discount = $DB->real_escape_string($_POST['discount']);
            $maxuses = $DB->real_escape_string($_POST['maxuses']);
            $bookid = $DB->real_escape_string($_POST['bookid']);

            $check = "SELECT id FROM library WHERE id='$bookid' AND owner='$user_id'";
            $rcheck = $DB->query($check);
            if (!$test = $rcheck->fetch_assoc()) {
                $err = 'You cannot create a code for this book.';
            } else {
                $pass = $DB->real_escape_string(md5($password));
                $SQL = "INSERT INTO promocodes (code,free,maxuses,discount,bookid) VALUES ('$code','$free', '$maxuses','$discount','$bookid')";
                $RES = $DB->query($SQL);
                $success = 'Coupon added successfully!';
            }
        }
        if (isset($_POST['deletecode'])) {
            $code = $DB->real_escape_string($_POST['code']);

            if ($code != '') {
                $SQL = "DELETE FROM promocodes WHERE id='$code'";
                $RES = $DB->query($SQL);
                $success = 'Coupon deleted!';
            }
        }

        include 'menu.php';
        ?>
<div id="app-container">
    <?php if (isset($err)) {
            ?>
    <div class="alert alert-danger alert-dismissable">
        <h4>
            Error!
        </h4> <?php echo htmlspecialchars($err); ?>
    </div>
    <?php
        }
    ?>
    <?php
        if (isset($success)) {
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
            <h1 style="font-size:28px;">Promocodes</h1>
            <p>Here you can add and delete coupon codes</p>

        </div>
    </div>

    <div class="col-md-4">
        <div id="tab-user-register" class="tab-pane active">

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Add coupon</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="" method="POST">
                        <div class="form-group">
                            <label for="register-name">Code:</label>
                            <input type="text" class="form-control" id="register-name" value="" name="code"
                                required="required">
                        </div>

                        <div class="form-group">
                            <label for="registertype">Free book / Discount</label>
                            <select class="form-control" name="free" id="registertype">
                                <option value="0">Discount</option>
                                <option value="1">Free book</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="register-maxsessions">Usable X times (-1 for unlimited uses)</label>
                            <input type="number" class="form-control" id="register-maxsessions" name="maxuses"
                                value="-1" required="required">
                        </div>
                        <div class="form-group">
                            <label for="register-maxsessions">Discount X% (0-100)</label>
                            <input type="number" class="form-control" id="register-maxsessions" name="discount"
                                value="0" required="required">
                        </div>

                        <div class="form-group">
                            <label for="registeradmin">Select book:</label>
                            <select class="form-control" name="bookid" id="registeradmin">
                                <?php
                                        echo '<option value="">-- choose book --</option>';
                                        $user_id = $_SESSION['user_id'];
                                        $SELECTBOOKS = "SELECT * FROM library WHERE price <> '0' AND owner ='$user_id'";
                                        $RB = $DB->query($SELECTBOOKS);
                                        while ($book = $RB->fetch_assoc()) {
                                            echo '<option value="'.$book['id'].'">'.htmlspecialchars($book['title']).'</option>';
                                        }
                                    ?>
                            </select>
                        </div>
                        <button type="submit" name="addcode" id="addcodebutton" tabindex="1000" class="btn btn-primary"
                            title="Add coupon" aria-label="Sign up">Add Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="tab-users" class="tab-pane active">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Coupon codes</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Book</th>
                                <th>Free/Discount</th>
                                <th>Discount %</th>
                                <th>Uses left</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $SQL = "SELECT p.id as id, p.discount as discount, p.maxuses as maxuses, p.free as free, p.code as code, l.title as title FROM promocodes as p, library as l WHERE p.bookid = l.id AND l.owner='$user_id'";
                                $RES = $DB->query($SQL);
                                while ($pr = $RES->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>'.htmlspecialchars($pr['code']).'</td> ';
                                    echo '<td>'.htmlspecialchars($pr['title']).'</td> ';
                                    $acc = 'Free';
                                    if ($pr['free'] == 0) {
                                        $acc = 'Discount';
                                    }
                                    echo '<td>'.$acc.'</td> ';
                                    echo '<td>'.$pr['discount'].'%</td>  ';
                                    echo '<td>'.$pr['maxuses'].'</td>';
                                    echo '<td><form action="" method="post"><input type="hidden" name="code" value="'.$pr['id'].'"><input type="submit" class="btn btn-warning" value="Delete" name="deletecode"></form></td>';
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