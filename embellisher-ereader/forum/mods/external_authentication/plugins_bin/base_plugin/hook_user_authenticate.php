<?php
/*
    This file is used if the admin has still enabled Phorum login and your 
    external application uses a different method than Phorum to create the 
    md5 password.
    
    Please note, that any time you want to end the script (perhaps because an 
    error occurs) you must use the command: return $auth_data;
    
    Below is an example of various possible needs for your plugin as taken from 
    the Elgg 1.1 plugin where Elgg adds a random string to the end of the 
    password which the user types into the login form.  I used a custom profile 
    field to store that random string (which Elgg refers to as salt).
    
*/

// Make sure that this script is loaded inside the Phorum environment.  DO NOT 
// remove this line
if (!defined("PHORUM")) return;

// no need to continue if the admin has disabled Phorum login
if (!empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"]))
    return $auth_data;

// bring in the api code for various user functions
include_once("./include/api/user.php");

// use the given username to get a Phorum user_id
$user_id = phorum_api_user_search("username",$auth_data["username"]);
// use the user_id to get that Phorum user's data
if (!empty($user_id)) $user_data = phorum_api_user_get($user_id);

// if the given username is a Phorum user and the Elgg salt has been set for 
// that user, add it to the given password
if (!empty($user_data["phorum_mod_external_authentication_elgg_1_1_user_salt"]))
    $auth_data["password"] .= $user_data["phorum_mod_external_authentication_elgg_1_1_user_salt"];

// the phorum_api_user_authenticate function will return a user_id if the given 
// username and password are correct. Be sure to pass the $auth_data["type"] 
// field.  If the username or password are incorrect, this function returns
// FALSE. Phorum then takes the auth_data and if the user_id is an actual id, 
// a session is created for that user.
$auth_data["user_id"] = phorum_api_user_authenticate($auth_data["type"],$auth_data["username"],$auth_data["password"]);

?>
