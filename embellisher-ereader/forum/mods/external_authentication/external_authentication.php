<?php

if (!defined("PHORUM")) return;

function phorum_mod_external_authentication_after_register($user_data) {
    
    global $PHORUM;
    
    //run the user_authenticate code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["after_register"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_after_register.php");
    }
    
    return $user_data;
}

function phorum_mod_external_authentication_before_footer() {
    
    global $PHORUM;
    
    //no need to continue if the admin does not want to hide any links
    if (empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        || (empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"])
            && empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])
            && empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"]))) return;
    
    //prepare the javascript content
    $content = "<script type=\"text/javascript\">\n";
    
    if (!empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"])) {
        $loginout_url = phorum_get_url(PHORUM_LOGIN_URL);
        $content .= "var disable_phorum_logout = true;\nvar phorum_logout_url = \"$loginout_url\";\n";
    } else {
        $content .= "var disable_phorum_logout = false;\nvar phorum_logout_url = \"\";\n";
    }
    if (!empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])) {
      $loginout_url = phorum_get_url(PHORUM_LOGIN_URL);
        $content .= "var disable_phorum_login = true;\nvar phorum_login_url = \"$loginout_url\";\n";
    } else {
        $content .= "var disable_phorum_login = false;\nvar phorum_login_url = \"\";\n";
    }
    if (!empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"])) {
        $register_url = phorum_get_url(PHORUM_REGISTER_URL);
        $content .= "var disable_phorum_registration = true;\nvar phorum_register_url = \"$register_url\";\n";
    } else {
        $content .= "var disable_phorum_registration = false;\nvar phorum_register_url = \"\";\n";
    }
    
    //grab the rest of the javascript
    $content .= file_get_contents("./mods/external_authentication/js_bin/disable_links.js");
    $content .= "</script>";
    
    //print out the javascript
    print $content;
    
    return;
    
}

function phorum_mod_external_authentication_common_post_user() {
    
    global $PHORUM;
    
    //run the user_authenticate code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["common_post_user"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_common_post_user.php");
    }
    
}

function phorum_mod_external_authentication_common() {
    
    global $PHORUM;
    
    //run the user_authenticate code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["common"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_common.php");
    }
    
}
    
function phorum_mod_external_authentication_css_register($data) {

    global $PHORUM;
    
    if (!empty($PHORUM['phorum_mod_external_authentication']['css_version']))
        $data['register'][] = array(
            "module"    => "external_authentication",
            "where"     => "after",
            "source"    => "function(phorum_mod_external_authentication_empty_css)",
            "cache_key" => $PHORUM['phorum_mod_external_authentication']['css_version']
        );
    
    return $data;
    
}

function phorum_mod_external_authentication_empty_css() { return ""; }

function phorum_mod_external_authentication_start_output() {
    
    global $PHORUM;
    
    //hide the logout option, if the admin wants that
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        && !empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"])) {
        $PHORUM["DATA"]["LANG"]["LogOut"] = "";
        $PHORUM["DATA"]["URL"]["LOGINOUT"] = "";
    }
    
    //hide the login option, if the admin wants that
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        && !empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])) {
        $PHORUM["DATA"]["LANG"]["LogIn"] = "";
        $PHORUM["DATA"]["URL"]["LOGINOUT"] = "";
    }
    
    //if the admin has disabled registration, hide the registration option
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        && !empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"])
        && empty($PHORUM["DATA"]["LOGGEDIN"])) {
        $PHORUM["DATA"]["LANG"]["Register"] = "";
        $PHORUM["DATA"]["URL"]["REGISTERPROFILE"] = "";
    }
    
    //run the user_authenticate code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["start_output"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_start_output.php");
    }
    
    return;
}

function phorum_mod_external_authentication_page_login() {
    
    global $PHORUM;
    
    //if the admin has disabled login, redirect to the index
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        && !empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])) {
        phorum_redirect_by_url($PHORUM["http_path"]);
        exit;
    }
        
}

function phorum_mod_external_authentication_posting_custom_action($message) {
    
    global $PHORUM;
    
    //hide the login option, if the admin wants that
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        && !empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])
        && !empty($PHORUM["DATA"]["CLICKHEREMSG"])
        && $PHORUM["DATA"]["CLICKHEREMSG"] == $PHORUM["DATA"]["LANG"]["ClickHereToLogin"]) {
        unset($PHORUM["DATA"]["CLICKHEREMSG"]);
        unset($PHORUM["DATA"]["URL"]["CLICKHERE"]);
    }
    
    return $message;
  
}

function phorum_mod_external_authentication_page_register() {
    
    global $PHORUM;
    
    //if the admin has disabled registration, redirect to the index
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])
        && !empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"])) {
        phorum_redirect_by_url($PHORUM["http_path"]);
        exit;
    }
        
}

function phorum_mod_external_authentication_user_authenticate($auth_data) {
    
    global $PHORUM;
    
    //run the user_authenticate code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["user_authenticate"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_user_authenticate.php");
    }
    
    return $auth_data;
}

function phorum_mod_external_authentication_user_delete($user_id) {
    
    global $PHORUM;
    
    //run the user_delete code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["user_delete"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_user_delete.php");
    }
    
    return $user_id;
}

function phorum_mod_external_authentication_user_save($user_data) {
    
    global $PHORUM;
    
    //run the user_save code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["user_save"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_user_save.php");
    }
    
    return $user_data;
}

function phorum_mod_external_authentication_user_session_restore($session_data) {

    //no need to continue if we are in the admin section
    if (defined("PHORUM_ADMIN")) return $session_data;
    
    global $PHORUM;
    
    //run the user_session_restore code for the selected external app if applicable
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]["user_session_restore"]))) {
        
        // load the synchronization table code if necessary
        if ($PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"]) {
            include_once "./mods/external_authentication/db_bin/db_functions.php";
        }
        
        include_once("./mods/external_authentication/plugins_bin/{$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]}/hook_user_session_restore.php");
    }

    return $session_data;
}

?>
