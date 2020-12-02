<?php

$DB = new mysqli('localhost', 'root', '', 'ereader'); //bssoluti_test gtBvn&U0xUJZ
if ($DB->connect_errno) {
    echo 'Failed to connect to MySQL: ('.$DB->connect_errno.') '.$DB->connect_error;
}
$DB->set_charset('utf8');
$salt = '$2a$07$hallothisisa22stringha$';

$stripeKey = 'sk_live_F5fvqmJdFbtH6rRnR4VUJbW1';

$VIEWURL = 'http://james.com/embellisher-ereader/?epub=/creator/';
$STOREURL = 'http://james.com/embellisher-ereader/?bookid=';

$BASE_SERVER = 'http://james.com';
$APP_LOC = '/creator';

$SERVERURL = $BASE_SERVER.$APP_LOC.'/';

define('EMAIL_FROM', 'noreply@emrepublishing.com');
//direction, either rtl or ltr
define('DIR', 'ltr');
//set to 1 if registration is open
define('REGISTRATION_OPEN', 1);
