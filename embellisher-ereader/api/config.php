<?php

//ENTER THE URL OF THE main folder here
define('SERVERURL', 'http://james.com/embellisher-ereader/');

//if you have the embellisher creator fill in the path here
define('CREATORRELURL', 'http://james.com/creator/');

//if we have phplist enable it by setting the path
//define("PHPLIST", '../../lists');
define('PHPLIST', 'http://james.com/embellisher-ereader/admin');

//if true then each admin account has its own store.
//Users that register have to choose the store to which they register
define('SEPARATE_ADMINS', true);

//Database configuration. Enter the location of the database, database user, password, database name
//$DB = new mysqli("localhost", "username", "password", "databasename");
$DB = new mysqli('localhost', 'root', '', 'ereader');

//TILL HERE
$SERVERURL = SERVERURL.'admin/';
$salt = '$2a$07$hallothisisa22stringha$';
if ($DB->connect_errno) {
    echo 'Failed to connect to MySQL: ('.$DB->connect_errno.') '.$DB->connect_error;
}
$DB->set_charset('utf8');
//do not change this
define('PREMIUM', false);
