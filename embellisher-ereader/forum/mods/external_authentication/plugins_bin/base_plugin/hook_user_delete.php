<?php
/*
    This file is used if the admin deletes a user, right before the user is 
    actually deleted.
    
    Please note, that any time you want to end the script (perhaps because an 
    error occurs) you must use the command: return $user_id;
    
    Below is an example of how to delete a user from the user id synchronization
    table.
    
*/

// Make sure that this script is loaded inside the Phorum environment.  DO NOT 
// remove this line
if (!defined("PHORUM")) return;

// call the delete_user function supplying the Phorum user id
phorum_mod_external_authentication_db_delete_user ($user_id, MOD_EXT_AUTH_DELETE_PHORUM_USER);

?>
