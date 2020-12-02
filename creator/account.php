<?php
    require 'header.php';
    require_once 'includes/functions.php';

    $floatDirection = 'left';

    $userid = $_SESSION['user_id'];
    $user = array();

    $SEL = "SELECT * FROM user WHERE id='$userid'";
    $res = $DB->query($SEL);
    if ($U = $res->fetch_assoc()) {
        $user = $U;
    } else {
        $err = _('Error: Could not get user account.');
    }
    $email = trim($user['email']); // "MyEmailAddress@example.com"
    $email = strtolower($email); // "myemailaddress@example.com"
    $emailhash = md5($email);

    include 'menu.php';
?>
<div id="app-container">
    <?php if (isset($err)) {
    ?>
    <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
    <div class="alert alert-danger alert-dismissable">

        <h4>
            <?php echo _('Warning!'); ?>
        </h4> <?php echo htmlspecialchars($err); ?>
    </div>
    <?php
} ?>
    <div class="container">
        <div class="md-12" style="height:60px;">

        </div>
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 id="usernametitle" class="panel-title"><?php echo htmlspecialchars($user['name']); ?></h3>
                <h4 id="usernametype" class="panel-subtitle"><?php echo htmlspecialchars($user['type']); ?></h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3" text-align="center"> 
						<img alt="User Pic" src="https://www.gravatar.com/avatar/<?php echo $emailhash; ?>"
                            class="img-circle profilepicture"><br /><br /><a style="font-size:8pt;"
                            href="http://en.gravatar.com" target="_blank"><?php echo _('Change gravatar'); ?></a> </div>

                    <div class=" col-md-9 col-lg-9 ">
                        <table class="table table-user-information">
                            <tbody>
                                <tr class="AuthorSetting">
                                    <td style="text-align: left;"><?php echo _('Genre of writing'); ?>:</td>
                                    <td style="text-align: left;" id="usergenre">
                                        <?php echo htmlspecialchars($user['genre_of_writing']); ?></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;"><?php echo _('Interests'); ?>:</td>
                                    <td style="text-align: left;" id="userinterest">
                                        <?php echo htmlspecialchars($user['interests']); ?></td>
                                </tr>

                                <tr>
                                    <td style="text-align: left;"><?php echo _('Email'); ?>:</td>
                                    <td style="text-align: left;"><a id="useremail"
                                            href="{{user.email}}"><?php echo htmlspecialchars($user['email']); ?></a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <a href="editaccount.php"
                            class="btn btn-primary AuthorSetting"><?php echo _('Edit account'); ?></a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
loadhome();
</script>

</html>