<?php

if (!defined("PHORUM")) return;

define ("MOD_EXT_AUTH_GET_PHORUM_USER", "phorum_user_id");
define ("MOD_EXT_AUTH_GET_EXTERNAL_USER", "external_user_id");

define ("MOD_EXT_AUTH_DELETE_PHORUM_USER", "phorum_user_id");
define ("MOD_EXT_AUTH_DELETE_EXTERNAL_USER", "external_user_id");

// add a user to the synchronization table
// input: unique Phorum user id, unique external user id
// output: none
function phorum_mod_external_authentication_db_add_user ($phorum_user_id, $external_user_id) {
    
    // if either of the ids are empty or not integers, we can't continue
    if (empty($phorum_user_id) || !is_int($phorum_user_id)) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error adding user to the synchronization table: Invalid Phorum user id.",
                "details"   => "User id:\n$phorum_user_id",
            ));
        }
        return;
    } else if (empty($external_user_id) || !is_int($external_user_id)) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error adding user to the synchronization table: Invalid external user id.",
                "details"   => "User id:\n$external_user_id",
            ));
        }
        return;
    }
    
    global $PHORUM;
    
    phorum_mod_external_authentication_db_check_table($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]);
    
    $synchronization_table = $PHORUM["phorum_mod_external_authentication"]["synchronization_table_name"][$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]];
    
    $sql = "INSERT INTO $synchronization_table
            (phorum_user_id, external_user_id)
            VALUES ($phorum_user_id, $external_user_id)";
        
    $error = phorum_db_interact(DB_RETURN_ERROR, $sql, NULL, DB_MASTERQUERY);
    if ($error !== NULL) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error adding user to the synchronization table",
                "details"   => "SQL:\n$sql\n\nError:\n$error",
            ));
        }
    }
}

// input: user id, source (MOD_EXT_AUTH_GET_EXTERNAL_USER or MOD_EXT_AUTH_GET_PHORUM_USER)
// output: synchronized user id from source (either Phorum or external 
// application) 
function phorum_mod_external_authentication_db_get_user ($user_id, $source) {
    
    // if the id is empty or not an integer, we can't continue
    if (empty($user_id) || !is_int($user_id)) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error getting user from the synchronization table: Invalid user id.",
                "details"   => "User id:\n$user_id\n".gettype($user_id)."\nSource:\n$source\nPage:\n".phorum_page,
            ));
        }
        return NULL;
    }
    
    global $PHORUM;
    
    phorum_mod_external_authentication_db_check_table($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]);
    
    $synchronization_table = $PHORUM["phorum_mod_external_authentication"]["synchronization_table_name"][$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]];
    
    $supplied = ($source == MOD_EXT_AUTH_GET_EXTERNAL_USER) 
        ? MOD_EXT_AUTH_GET_PHORUM_USER 
        : MOD_EXT_AUTH_GET_EXTERNAL_USER;
    
    $sql = "SELECT $source from $synchronization_table
            WHERE $supplied = $user_id";
    
    $return_id = phorum_db_interact(DB_RETURN_VALUE, $sql);
    
    // if the id is empty or not an integer, we can't continue
    if (empty($return_id) || ($return_id != $return_id + 0)) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error getting user from the synchronization table: Invalid return user id.",
                "details"   => "SQL:\n$sql\n\nResult:\n$return_id",
            ));
        }
        return NULL;
    }
    
    return $return_id + 0;
    
}

// input: user id, source (MOD_EXT_AUTH_DELETE_EXTERNAL_USER or MOD_EXT_AUTH_DELETE_PHORUM_USER)
// output: none
function phorum_mod_external_authentication_db_delete_user ($user_id, $source) {
    
    // if the id is empty or not an integer, we can't continue
    if (empty($user_id) || !is_int($user_id)) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error deleting user from the synchronization table: Invalid user id.",
            ));
        }
        return;
    }
    
    global $PHORUM;
    
    phorum_mod_external_authentication_db_check_table($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]);
    
    $synchronization_table = $PHORUM["phorum_mod_external_authentication"]["synchronization_table_name"][$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]];
    
    $sql = "DELETE FROM $synchronization_table
            WHERE $source = $user_id";
        
    $error = phorum_db_interact(DB_RETURN_ERROR, $sql, NULL, DB_MASTERQUERY);
    if ($error !== NULL) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error deleting user from the synchronization table",
                "details"   => "SQL:\n$sql\n\nError:\n$error",
            ));
        }
    }
    
}

// check the existence of the synchronization table for the current external
// application.  If the table name has not been saved to the database, do so. If
// the table does not exist, create it.
// input: the external application folder (eg. base_plugin)
// output: none
function phorum_mod_external_authentication_db_check_table ($postfix) {

    global $PHORUM;
    
    // if the sync table is not flagged as created we will need to create it.
    if (empty($PHORUM["phorum_mod_external_authentication"]["synchronization_table_name"][$postfix])) {
        // pull in the database configuration
        require_once("./include/db/config.php");
        
        // set the full synchronization table name
        $synchronization_table = $PHORUM["DBCONFIG"]["table_prefix"]."_phorum_mod_external_authentication_".$postfix;
        
        // update the settings to indicate that the table has been created.
        $PHORUM["phorum_mod_external_authentication"]["synchronization_table_name"][$postfix] = $synchronization_table;
        phorum_db_update_settings(array("phorum_mod_external_authentication"=>$PHORUM["phorum_mod_external_authentication"]));
    } else {
        $synchronization_table = $PHORUM["phorum_mod_external_authentication"]["synchronization_table_name"][$postfix];
    }
    // make sure the synchronization table exists
    $sql = "SELECT * from $synchronization_table";
    $error = phorum_db_interact(DB_RETURN_ERROR, $sql, NULL, DB_MASTERQUERY);
    
    if (!empty($error)) {
        // create the synchronization table
        phorum_mod_external_authentication_db_create_table ($synchronization_table);
    }
    
}

// create the synchronization table.
// input: synchronization table name
// output: none
function phorum_mod_external_authentication_db_create_table ($synchronization_table) {
    
    $sql = "CREATE TABLE $synchronization_table (
            sync_id           int unsigned NOT NULL auto_increment,
            phorum_user_id    int unsigned NOT NULL,
            external_user_id  int unsigned NOT NULL,
            PRIMARY KEY (sync_id),
            UNIQUE phorum_user_id (phorum_user_id),
            UNIQUE external_user_id (external_user_id)
            )";
    
    $error = phorum_db_interact(DB_RETURN_ERROR, $sql, NULL, DB_MASTERQUERY);
    if ($error !== NULL) {
        //log the error if enabled
        if (function_exists('event_logging_writelog')) {
            event_logging_writelog(array(
                "message"	=> "Error creating synchronization table",
                "details"   => "SQL:\n$sql\n\nError:\n$error",
            ));
        }
    }
    
}
