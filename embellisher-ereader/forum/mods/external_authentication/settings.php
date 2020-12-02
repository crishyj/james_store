<?php

// Make sure that this script is loaded from the admin interface.
if(!defined("PHORUM_ADMIN")) return;

global $PHORUM;

// Save settings in case this script is run after posting
// the settings form.
if(count($_POST)) 
{
    // by default, allow the option to transfer admin status to Phorum and to 
    // disable/enable Phorum registration, login, and logout but set them to 
    // disabled
    $PHORUM["phorum_mod_external_authentication"]["remove_transfer_admin_status"] = 0;
    $PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_logout"] = 0;
    $PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_login"] = 0;
    $PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_registration"] = 0;
    $PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"] = 1;
    $PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"] = 1;
    $PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"] = 1;
    
    $cur_plugin = (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) ? $PHORUM["phorum_mod_external_authentication"]["mod_app_folder"] : "";
    
    // process the POSTed settings
    $PHORUM["phorum_mod_external_authentication"]["mod_app_folder"] = empty($_POST["mod_app_folder"]) ? "" : $_POST["mod_app_folder"];
    $PHORUM["phorum_mod_external_authentication"]["app_path"] = empty($_POST["app_path"]) ? "" : $_POST["app_path"];
    // only process the following options if a plugin is and has not been changed
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) 
        && $PHORUM["phorum_mod_external_authentication"]["mod_app_folder"] == $cur_plugin) {
        $PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"] = empty($_POST["disable_phorum_logout"]) ? 0 : 1;
        $PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"] = empty($_POST["disable_phorum_login"]) ? 0 : 1;
        $PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"] = empty($_POST["disable_phorum_registration"]) ? 0 : 1;
        $PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"] = empty($_POST["transfer_admin_status"]) ? 0 : 1;
    }
    
    // gather application data if an external application has been chosen
    if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) {
        
        // clear the current list of external application hooks
        unset($PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"]);

        // gather a list of hooks for the currently selected external 
        // application
        $possible_app_hooks = scandir("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]);
        foreach ($possible_app_hooks as $hook) {
            //remove the guaranteed non-hooks
            if (!preg_match("/^hook_./", $hook)) continue;
            preg_match("/^hook_([\w\W]+)\.php?$/", $hook, $m);
            $hook = $m[1];
            $PHORUM["phorum_mod_external_authentication"]["mod_app_hooks"][$hook] = 1;
        }
        
        // pull in settings post/defaults code for the selected external app 
        // (if applicable)
        if (file_exists("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/settings_post.php")) {
            include_once("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/settings_post.php");
            
            // enable the synchronization table if requested by the plugin
            if (!empty($utilize_synchronization_table)) {
                include_once "./mods/external_authentication/db_bin/db_functions.php";
                phorum_mod_external_authentication_db_check_table($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]);
                
                $PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"] = TRUE;
            // otherwise disable the table
            } else {
                $PHORUM["phorum_mod_external_authentication"]["utilize_synchronization_table"] = FALSE;
            }
            
            // search for the external application's path if not provided by the
            // user or if the external application has changed and if it is 
            // enabled by the plugin
            if ((empty($PHORUM["phorum_mod_external_authentication"]["app_path"]) || $PHORUM["phorum_mod_external_authentication"]["mod_app_folder"] != $cur_plugin)
                && !empty($external_application_path_ids)) {
                

                $phorum_path = getcwd();
                
                chdir("..");

                // search recursively for the unique files
                $unique_files = rglob($external_application_path_ids["unique_file_name"]);
                
                if (count($unique_files) > 0) {
                
                    // check for the unique string if necessary
                    if (!empty($external_application_path_ids["unique_string"])) {
                        foreach ($unique_files as $possible_file) {
                            if (strpos(file_get_contents("./".$possible_file), $external_application_path_ids["unique_string"]) !== FALSE) {
                                $app_path = getcwd()."/".str_replace("/".$external_application_path_ids["unique_file_name"], "", $possible_file);
                                break;
                            }
                        }
                    } else if (count($unique_files) == 1) {
                        $app_path = getcwd()."/".str_replace("/".$external_application_path_ids["unique_file_name"], "", $unique_files[0]);
                    }
                }
                
                if (!empty($app_path))
                    $PHORUM["phorum_mod_external_authentication"]["app_path"] = $app_path;
                
                chdir($phorum_path);
            }
        }
    }
    
    phorum_db_update_settings(array("phorum_mod_external_authentication"=>$PHORUM["phorum_mod_external_authentication"]));
    phorum_admin_okmsg("Settings Updated");

}

// set defaults
if (empty($PHORUM["phorum_mod_external_authentication"]["defaults_set"])) {
    $PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"] = 1;
    $PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"] = 1;
    $PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"] = 1;
    $PHORUM["phorum_mod_external_authentication"]["defaults_set"] = 1;
    
    phorum_db_update_settings(array("phorum_mod_external_authentication"=>$PHORUM["phorum_mod_external_authentication"]));
}

// set a blank possible app to show if none are selected
$PHORUM["phorum_mod_external_authentication"]["possible_apps"][] = array("name" => " ", "app_folder" => "");

// set the current version of this module
$version = "5.2.1.04";

// pull in the version checking code
include_once("./mods/external_authentication/settings_bin/version_check.php");
// parse the current module version
$parsed_version = phorum_mod_external_authentication_parse_version($version);

// get a list of possible apps
$possible_app_dirs = scandir("./mods/external_authentication/plugins_bin");

// loop through the available apps
foreach($possible_app_dirs as $dir) {
    if ($dir == "." || $dir == ".." || $dir == ".svn") continue;
    // and pull in their info if possible
    if (file_exists("./mods/external_authentication/plugins_bin/$dir/info.php"))
        include_once("./mods/external_authentication/plugins_bin/$dir/info.php");
}

$possible_apps_errors = array();
// pull the possible apps info together for the dropdown selection
foreach($PHORUM["phorum_mod_external_authentication"]["possible_apps"] as $key => $possible_app_info) {
    if (!empty($possible_app_info["required_version"])) {
        
        // run a required version comparison on the possible apps
        $parsed_required_version = phorum_mod_external_authentication_parse_version($possible_app_info["required_version"]);
        $vcomp = phorum_mod_external_authentication_compare_versions($parsed_version, $parsed_required_version);
        // if the required version is newer than the current version, flag an 
        // error and do not add the app to the selection list
        if (!empty($vcomp) && $vcomp < 0) {
            $possible_apps_errors[] = $possible_app_info["name"]." requires version ".$possible_app_info["required_version"];
            continue;
        }
    }
    
    $possible_apps[$possible_app_info["app_folder"]] = $possible_app_info["name"];
    
    if (!empty($possible_app_info["author"]) && $possible_app_info["author"] != "Joe Curia") 
        $possible_apps[$possible_app_info["app_folder"]] .= " (Created by ".$possible_app_info["author"].")"; 
}

unset ($PHORUM["phorum_mod_external_authentication"]["possible_apps"]);

// show an error for apps that need a newer version of this module
if (!empty($possible_apps_errors)) {
    phorum_admin_error("The following external application(s) require a newer version of this module:<br>".implode(", ",$possible_apps_errors));
}

//Sort the possible apps
asort($possible_apps);

// get the current server path to show as an example
$curcwd = getcwd();

// We build the settings form by using the PhorumInputForm object. When
// creating your own settings screen, you'll only have to change the
// "mod" hidden parameter to the name of your own module.
include_once "./include/admin/PhorumInputForm.php";
$frm = new PhorumInputForm ("", "post", "Save");
$frm->hidden("module", "modsettings");
$frm->hidden("mod", "external_authentication"); 

// This adds a break line to your form, with a description on it.
// You can use this to separate your form into multiple sections.
$frm->addbreak("Edit settings for the External Authentication module");
$row=$frm->addrow("Which external application will be the authentication master:", $frm->select_tag("mod_app_folder", $possible_apps, $PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]));
$frm->addhelp($row,"External Application as Authentication Master","Please choose the application you would like to use as the authentication master.  If your application is not available, please request it.  Your authentication master will be the main registration and login system.  It will then pass user names on to Phorum.");
$row=$frm->addrow("What is the path for the external application:", $frm->text_box("app_path", $PHORUM["phorum_mod_external_authentication"]["app_path"], "","","",""));
$frm->addhelp($row,"External Application Path","Please enter the server path to the root of your external application.<br/><br/>As a reference point, your Phorum server path is:<br/>$curcwd");
// allow the admin to disable/enable transferring admin status to Phorum if not 
// preset by the currently selected plugin
$disabled = (empty($PHORUM["phorum_mod_external_authentication"]["remove_transfer_admin_status"])
    && !empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) ? "" : "disabled='disabled'"; 
$row=$frm->addrow("Automatically transfer admin status: ", $frm->checkbox("transfer_admin_status", "1", "", $PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"], $disabled));
$frm->addhelp($row,"Transfer Admin Status","With this enabled, if a user is an admin in your external application, they will be made an admin in Phorum.  Upgrades to and downgrades from admin status will also be transferred.");

// allow the admin to disable/enable Phorum logout if not preset by the 
// currently selected plugin
$disabled = (empty($PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_logout"])
    && !empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) ? "" : "disabled='disabled'"; 
$row=$frm->addrow("Disable showing the Phorum logout links: ", $frm->checkbox("disable_phorum_logout", "1", "", $PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"], $disabled));
$frm->addhelp($row,"Disable Logout links","This option will remove all logout links from the Phorum pages.");

// allow the admin to disable/enable Phorum login if not preset by the 
// currently selected plugin
$disabled = (empty($PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_login"])
    && !empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) ? "" : "disabled='disabled'"; 
$row=$frm->addrow("Disable Phorum login: ", $frm->checkbox("disable_phorum_login", "1", "", $PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"], $disabled));
$frm->addhelp($row,"Disable Phorum Login","This option will remove all login links from the Phorum pages and disable Phorum login.");

// allow the admin to disable/enable Phorum registration if not preset by the 
// currently selected plugin
$disabled = (empty($PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_registration"])
    && !empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) ? "" : "disabled='disabled'"; 
$row=$frm->addrow("Disable Phorum registration: ", $frm->checkbox("disable_phorum_registration", "1", "", $PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"], $disabled));
$frm->addhelp($row,"Disable Phorum Registration","This option will remove all registration links from the Phorum pages and disable Phorum registration.");

//pull in settings code for the selected external app (if applicable)
if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]) && file_exists("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/settings.php")) {
    include_once("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/settings.php");
}

// We are done building the settings screen.
// By calling show(), the screen will be displayed.
$frm->show();

function rglob($pattern='*', $flags = 0, $path='')
{
    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files=glob($path.$pattern, $flags);
    foreach ($paths as $path) { $files=array_merge($files,rglob($pattern, $flags, $path)); }
    return $files;
}
?>