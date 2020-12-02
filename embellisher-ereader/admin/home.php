<?php
    require 'header.php';
    include 'menu.php';

    if (isset($_POST['savestripe'])) {
        $publickey = $DB->real_escape_string($_POST['publickey']);
        $privatekey = $DB->real_escape_string($_POST['privatekey']);
        $user_id = $_SESSION['user_id'];
        $SQL = "UPDATE user SET stripe_public = '$publickey', stripe_private = '$privatekey' WHERE id='$user_id'";
        if ($r = $DB->query($SQL)) {
            $sucess = 'Stripe settings updated.';
        } else {
            $err = 'Error updating stripe settings.';
        }
    }

    if(isset($_POST['savegenre'])){
        $genres = $DB->real_escape_string($_POST['genre']);
        $user_id = $_SESSION['user_id'];
        $SQL = "UPDATE user SET genres = '$genres' WHERE id='$user_id'";
        if ($r = $DB->query($SQL)) {
            $sucess = 'Category or Genre settings updated.';
        } else {
            $err = 'Error updating Category or Genre settings.';
        }
    }

    $publickey = '';
    $privatekey = '';
    $genres = '';
    $user_id = $_SESSION['user_id'];
    $SQL = "SELECT * FROM  user WHERE id='$user_id'";
    $r = $DB->query($SQL);
    $keys = $r->fetch_assoc();
    $privatekey = htmlentities($keys['stripe_private']);
    $publickey = htmlentities($keys['stripe_public']);
    $genres = htmlentities($keys['genres']);
    $_SESSION['genres'] = $genres;

?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1.1", {
    packages: ["bar"]
});
google.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Month', 'Sales', 'Buyers'],
        <?php
            $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Oct", "Sep", "Nov", "Dec");
            $m = 0;
            $SQL ="SELECT SUM(t.price/100) as monthsales, COUNT(t.price) as monthbuyers, MONTH(t.transactiondate)  as m FROM library as l, transactions as t WHERE l.owner = '$user_id' and l.id = t.libraryid GROUP BY MONTH(t.transactiondate) ORDER BY MONTH(t.transactiondate)";
            $RES = $DB -> query($SQL);
            $exist = false;
            while ($sold = $RES -> fetch_assoc()) {
                if ($sold['monthsales'] != "") {
                    for ($temp = $m; $temp < $sold['m'] - 1; $temp++) {
                        echo '["'.$months[$temp].
                        '",0,0],';
                    }
                    $exist = true;
                    $m = $sold['m'] - 1;
                    echo '["'.$months[$m].
                    '",'.$sold['monthsales'].
                    ','.$sold['monthbuyers'].
                    '],';
                }
            }
        ?>        
    ]);

    var options = {
        chart: {
            title: 'Monthly sales',
            subtitle: 'Sales, Buyers',
        },
        colors: ['green', 'blue'],
    };

    var chart = new google.charts.Bar(document.getElementById('chart_div'));
    chart.draw(data, options);
}
</script>

<div id="app-container">
    <?php 
        if (isset($err)) 
        {
    ?>
            <div class="alert alert-danger alert-dismissable">
                <h4>
                    Warning!
                </h4> <?php echo $err; ?>
            </div>
    <?php
        } 
    ?>
    <?php 
        if (isset($sucess)) 
        {
    ?>
        <div class="alert alert-success alert-dismissable">

            <h4>
                Success!
            </h4> <?php echo $sucess; ?>
        </div>
    <?php
        } 
    ?>

    <div class="jumbotron">
        <div class="container">

            <h2>Welcome <?php echo $_SESSION['display_name']; ?>,</h2>
            <p>Here you can manage your ebooks and view the latest transactions.</p>
            <?php if ($_SESSION['type'] == 1) 
            {
            ?>
                <a href="users.php" class="btn btn-primary">User management</a>
            <?php
            } ?>
            <a href="storeitems.php" class="btn btn-primary">Store management</a>
        </div>
    </div>

    <div class="col-md-12">
        <div class="container">

            <?php
                if ($_SESSION['type'] != 1) {
                    $sqlpub = "SELECT * FROM library WHERE owner = '$user_id'";
                    if (!$RESpub = $DB->query($sqlpub)) {
                        echo 'error in sql:'.$DB->error;
                        exit();
                    }
                    if (!$RESpub->fetch_assoc()) {
                        ?>
            <?php
                    }
                }
            ?>
            <?php
                $year_sales = 0;
                $month_sales = 0;
                $SQL = "SELECT SUM(t.price) as monthsales FROM library as l, transactions as t WHERE l.owner = '$user_id' and l.id = t.libraryid and t.transactiondate >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) ORDER BY t.transactiondate";
                $RES = $DB->query($SQL);
                if ($sold = $RES->fetch_assoc()) {
                    if ($sold['monthsales'] != '') {
                        $month_sales = $sold['monthsales'];
                    }
                }
                $SQL = "SELECT COUNT(t.price) as buyers FROM library as l, transactions as t WHERE l.owner = '$user_id' and l.id = t.libraryid and t.transactiondate >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) ORDER BY t.transactiondate";
                $RES = $DB->query($SQL);
                if ($sold = $RES->fetch_assoc()) {
                    if ($sold['buyers'] != '') {
                        $month_buyers = $sold['buyers'];
                    }
                }
                $SQL = "SELECT SUM(t.price) as yearsales FROM library as l, transactions as t WHERE l.owner = '$user_id' and l.id = t.libraryid and YEAR(t.transactiondate) = YEAR(CURDATE()) ORDER BY t.transactiondate DESC";
                $RES = $DB->query($SQL);
                if ($sold = $RES->fetch_assoc()) {
                    if ($sold['yearsales'] != '') {
                        $year_sales = $sold['yearsales'];
                    }
                }
                $SQL = "SELECT COUNT(t.price) as buyers FROM library as l, transactions as t WHERE l.owner = '$user_id' and l.id = t.libraryid and YEAR(t.transactiondate) = YEAR(CURDATE()) ORDER BY t.transactiondate DESC";
                $RES = $DB->query($SQL);
                if ($sold = $RES->fetch_assoc()) {
                    if ($sold['buyers'] != '') {
                        $year_buyers = $sold['buyers'];
                    }
                }
            ?>
            <div class="col-md-3" style="padding:20px;">
                <div class="img-rounded alert alert-info">
                    <center>
                        <h1>Buyers this month</h1>
                        <h2 style="font-size:2em;"><span class="glyphicon glyphicon-user"
                                aria-hidden="true"></span><?php echo ' '.$month_buyers; ?></h2>
                    </center>
                </div>
            </div>
            <div class="col-md-3" style="padding:20px;">
                <div class="img-rounded alert alert-success">
                    <center>
                        <h1>Total this month</h1>
                        <h2 style="font-size:2em;"><?php echo '$ '.(floatval($month_sales) / 100); ?></h2>
                    </center>
                </div>
            </div>
            <div class="col-md-3" style="padding:20px;">
                <div class="img-rounded alert alert-info">
                    <center>
                        <h1>Buyers this year</h1>
                        <h2 style="font-size:2em;"><span class="glyphicon glyphicon-user"
                                aria-hidden="true"></span><?php echo ' '.$year_buyers; ?></h2>
                    </center>
                </div>
            </div>
            <div class="col-md-3" style="padding:20px;">
                <div class="img-rounded alert alert-success">
                    <center>
                        <h1>Total this year</h1>
                        <h2 style="font-size:2em;"><?php echo '$ '.(floatval($year_sales) / 100); ?></h2>
                    </center>
                </div>
            </div>

            <div class="col-md-12" style="clear:both; height:40px;"></div>
            <?php 
                if ($exist) {
                    echo"    
                        <div class='col-md-12' style='clear:both; height:300px;' id='chart_div'></div>
                        ";
                }
            ?>

            <div class="panel panel-success" style="clear:both;">
                <div class="panel-heading">
                    <h2 class="panel-title">Last 200 sales</h2>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Book</th>
                                <th>Date</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $user_id = $_SESSION['user_id'];
                                $SQL = "SELECT u.name as customer, l.title as title, t.price, t.transactiondate as datum FROM library as l, transactions as t, user as u WHERE l.owner = '$user_id' and l.id = t.libraryid and u.id = t.userid ORDER BY t.transactiondate DESC LIMIT 200";
                                $RES = $DB->query($SQL);
                                while ($sell = $RES->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>'.htmlspecialchars($sell['customer']).'</td> ';
                                    echo '<td>'.htmlspecialchars($sell['title']).'</td> ';
                                    echo '<td>'.htmlspecialchars($sell['datum']).'</td> ';
                                    echo '<td>$ '.htmlspecialchars(floatval($sell['price']) / 100).'</td> ';
                                    echo '</tr> ';
                                }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h2 class="panel-title">Stripe settings</h2>
                </div>
                <div class="panel-body">
                    <p>To sell ebooks you need to apply for a <a href="https://stripe.com" target="_blank">stripe</a>
                        account and fill in your public and private key here.</p>
                    <form role="form" action="" method="post">
                        <div class="form-group AuthorSetting">
                            <label for="publickey">Stripe public key</label>
                            <input type="text" class="form-control" id="publickey" name="publickey"
                                value="<?php echo $publickey; ?>">
                        </div>
                        <div class="form-group">
                            <label for="privatekey">Stripe private key</label>
                            <input type="password" class="form-control" id="privatekey" name="privatekey"
                                value="<?php echo $privatekey; ?>">
                        </div>
                        <button type="submit" id="savepublisherdetails" name="savestripe" tabindex="1000"
                            class="btn btn-primary" title="save" aria-label="save">Save</button><br />
                    </form>
                </div>
            </div>
            <?php if ($_SESSION['type'] == 1) 
            {
            ?>
                <div class = "panel panel-info">
                    <div class="panel-heading">
                        <h2 class = "panel-title">Custom Category or Genre Settings</h2>
                        <br>
                        <p>If you will not input this field, the app will provide the default genres.</p>
                    </div>
                    <div class="panel-body">
                        <form action="" method = "post" role = "form">
                            <div class="form-group">
                                <label for="genres">Category or Genre</label>
                                <textarea name="genre" id="genre" rows="3" class = "form-control"><?php echo($genres);?></textarea>
                            </div>
                            <button type="submit" id="savegenre" name="savegenre" tabindex="1000"
                            class="btn btn-primary" title="save" aria-label="save">Save</button><br />
                        </form>
                    </div>
                </div>
            <?php
            }?>
        </div>
    </div>
</div>
</body>

</html>