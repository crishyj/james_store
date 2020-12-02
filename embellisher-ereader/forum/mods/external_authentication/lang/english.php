<?php

// pull in language code for the selected external app (if applicable)
if (!empty($PHORUM["phorum_mod_external_authentication"]["mod_app_folder"])) {
    // if the current language is supported by the active plugin, use that language
    if (file_exists("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/lang/".$PHORUM["language"].".php")) {
        include_once("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/lang/".$PHORUM["language"].".php");
    // otherwise get the default language if it is supported
    } elseif (file_exists("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/lang/".PHORUM_DEFAULT_LANGUAGE.".php")) {
        include_once("./mods/external_authentication/plugins_bin/".$PHORUM["phorum_mod_external_authentication"]["mod_app_folder"]."/lang/".PHORUM_DEFAULT_LANGUAGE.".php");
    }
}

?>
