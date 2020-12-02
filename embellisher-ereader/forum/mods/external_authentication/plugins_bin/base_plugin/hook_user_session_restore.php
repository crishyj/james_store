<?php
/*
    This file is used on each Phorum page to check the external application's 
    user session and create a session for the current user in Phorum.
    
    Please note, that any time you want to end the script (perhaps because an 
    error occurs) you must use the command: return $session_data;
    
    Below is an example of various possible needs for your plugin
    
*/

// Make sure that this script is loaded inside the Phorum environment.  DO NOT 
// remove this line
if (!defined("PHORUM")) return;

// If you need to run php code located in the external application's server path 
// you can use the following code as an example

// no need to continue if the external app path is not set.
if (empty($PHORUM["phorum_mod_external_authentication"]["app_path"])) return $session_data;

// save the working directory and move to the external application's directory
$curcwd = getcwd();
chdir($PHORUM["phorum_mod_external_authentication"]["app_path"]);

// include the necessary code from your external application
include_once("./user_api.php");

// get the session for the external application
$session = (!empty($_COOKIE["sessionid"])) ? $_COOKIE["sessionid"] : $_SESSION["sessionid"];
$email = (!empty($_COOKIE["email"])) ? $_COOKIE["email"] : $_SESSION["email"];
//echo "SESSION:".$session;
// get the user info from the external application
$user_data = get_user_function($session,$email);



// if there is no user data, then no need to continue
if (empty($user_data))  {
    // change back to the Phorum directory
    chdir($curcwd);
    // clear the previous session in case the user logged out of the external application and Phorum login is disabled
    if (!empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])) {
        $session_data[PHORUM_SESSION_LONG_TERM] = FALSE;
        $session_data[PHORUM_SESSION_SHORT_TERM] = FALSE;
    }
    return $session_data;
}

//switch back to our working directory
chdir($curcwd);

// get the api code for various user-related functions
include_once("./include/api/user.php");

// it is best to use the external application's username to authenticate to
// Phorum as that should be unique and avoids the hassle of dealing with 
// Phorum's serquential user_id assignment for new users
$username = $user_data["id"]."-".$user_data["name"];


// use the external username to get a Phorum user_id
$user_id = phorum_api_user_search("username",$username);
// then get the Phorum user data from that user_id
$phorum_user_data = phorum_api_user_get($user_id);

// if the Phorum user does not exist then we need to create them
if (empty($phorum_user_data)) {
    $phorum_user_data = array(
        // The user_id must be NULL to create a new user
        "user_id" => NULL,
        "username" => $username,
        // by transferring the password, we are ensuring that the user will be
        // able to login if the admin enables Phorum login
        "password" => MD5("*92zlMQMHzKx"),
        // Phorum requires an email.  If the external application does not, 
        // a fake email should be used.
        "email" => $username,
        // By default, create a non-admin user.  Admin status is handled later.
        "admin" => 0,
        "active" => PHORUM_USER_ACTIVE,
        );
   
    // if the admin wants to automatically transfer admin status
    if (!empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
        // and the user is an admin in the external application, then make the 
        // phorum user an admin.  Please note this is just and example.  Each 
        // application may have a different way to establish admin status
        if ($user_data["admin"] == 1) {
            $phorum_user_data["admin"] = 1;
        }
    }
    // create the new user and get the user_id with which to create a session.
    // Please note, most applications will give you the md5 of the user's 
    // password.  The constant PHORUM_FLAG_RAW_PASSWORD tells Phorum that the 
    // password is already in md5.  If you need to create a user with a plain
    // text password, simply omit the second variable in this call
    $user_id = phorum_api_user_save($phorum_user_data, PHORUM_FLAG_RAW_PASSWORD);
    
// however, if the user exists but is not active, then we should not log them in    
} elseif (empty($phorum_user_data["active"])) {
    return $session_data;
// or, if the user exists, then run some check on the user's data
} else {
    // if the extenal application user's password has changed, update the phorum 
    // user's password
    /*if ($phorum_user_data["password"] != $user_data["password"]) {
        $phorum_user_data["password"] = $user_data["password"];
        // save the updated user data, again with a preset md5 password
        $user_id = phorum_api_user_save($phorum_user_data,PHORUM_FLAG_RAW_PASSWORD);
    }*/
    
    // if the admin wants to automatically transfer admin status and the 
    // external user has been upgraded to admin, upgrade the phorum user, again 
    // assuming the external application establishes admin status this way
    if ($user_data["admin"]==1 && empty($phorum_user_data["admin"]) && !empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
        $phorum_user_data["admin"] = 1;
        // save the updates user data
        $user_id = phorum_api_user_save($phorum_user_data);
    // if the admin wants to automatically transfer admin status and the 
    // external user has been downgraded from admin, downgrade the phorum user
    } elseif ($user_data["admin"]==0 && !empty($phorum_user_data["admin"]) && !empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
        $phorum_user_data["admin"] = 0;
        // save the updates user data
        $user_id = phorum_api_user_save($phorum_user_data);
    }
}

//we have a legit user, so set there session info
$session_data[PHORUM_SESSION_LONG_TERM] = $user_id;
$session_data[PHORUM_SESSION_SHORT_TERM] = $user_id;

?>
