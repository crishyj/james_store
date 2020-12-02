<?php
    if (isset($_SESSION['originalid'])) {
        ?>
<div class="alert alert-danger alert-dismissable">
    <h4>
        Warning!
    </h4>
    You are logged in as another client, <a href="home.php?return=1">return to the admin area</a>.
</div>
<?php
    }
?>

<nav id="app-navbar" class="navbar" role="navigation">
    <div class="btn-group navbar-left">
        <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal"
            data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
            <span class="icon-readium" aria-hidden="true"></span>
        </button>
    </div>
    <div class="btn-group navbar-right">
        <a href="home.php" tabindex="1" type="button" class="btn icon-home" title="Dashboard" aria-label="home"
            style="width:auto;">
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span> Dashboard
        </a>
        <a href="<?php echo CREATORRELURL.'home.php'; ?>" target="_blank" tabindex="1" type="button" class="btn
            icon-edit" title="Creator studio" aria-label="home" style="width:auto;">
            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Creator Studio
        </a>
        <?php if ($_SESSION['type'] == 1) {
    ?>
        <a href="users.php" tabindex="1" type="button" class="btn icon-user" title="User management" aria-label="users"
            style="width:auto;">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Users
        </a>
        <?php
} ?>
        <a href="storeitems.php" tabindex="1" type="button" class="btn icon-store" title="Store" aria-label="store"
            style="width:auto;">
            <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Store
        </a>
        <a href="coupons.php" tabindex="1" type="button" class="btn icon-code" title="Promo codes" aria-label="users"
            style="width:auto;">
            <span class="glyphicon glyphicon-barcode" aria-hidden="true"></span> Coupons
        </a>
        <a href="logout.php" tabindex="1" type="button" class="btn icon-logout" title="logout" aria-label="logout"
            style="width:auto;">
            <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout
        </a>
    </div>
</nav>