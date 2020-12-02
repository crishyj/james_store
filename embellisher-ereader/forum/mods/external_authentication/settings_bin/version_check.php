<?php

// Make sure that this script is loaded from the admin interface.
if(!defined("PHORUM_ADMIN")) return;

//parse the version (based on code from the Phorum development team
function phorum_mod_external_authentication_parse_version ($version) {
    //match the four version numbers and any subrelease
    preg_match('/^(\d+)\.(\d+).(\d+).(\d+)([a-z])?$/', $version, $m);
    //convert the subrelease to a number
    $subrelease = empty($m[5]) ? 0 : ord($m[5])-96; // ord('a') = 97;
    //setup the parsed version as an array of numbers
    $parsed_version = array($m[1], $m[2], $m[3], $m[4], $subrelease);
    
    return $parsed_version;
}

function phorum_mod_external_authentication_compare_versions ($version1,$version2) {

    // Compare relevant parts of the parsed version numbers to see
    // what version is higher.
    for ($s=0; $s<=4; $s++) {
        if (!isset($version1[$s]) || !isset($version2[$s])) break;
        if ($version1[$s] > $version2[$s]) return +1;
        if ($version1[$s] < $version2[$s]) return -1;
    }
    
    return 0;
    
}

?>
