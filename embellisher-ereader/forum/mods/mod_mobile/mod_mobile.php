<?php

function mod_mobile(){

    global $PHORUM;

    if(empty($PHORUM["mod_mobile"]["template"])) return;

    $words = explode("\n", $PHORUM["mod_mobile"]["ua_keywords"]);

    foreach($words as $w){
        $w = trim($w);
        if(!empty($w)){
            if(strpos($_SERVER["HTTP_USER_AGENT"], $w) !== false){
                $PHORUM['template'] = $PHORUM["mod_mobile"]["template"];
                break;
            }
        }
    }

    if($PHORUM['template'] == "mobile"){

        if(phorum_page != "index" &&
           phorum_page != "list" &&
           phorum_page != "read" &&
           phorum_page != "search" &&
           phorum_page != "login" &&
           phorum_page != "register" &&
           phorum_page != "post" &&
           phorum_page != "pm" &&
           phorum_page != "file"
          ){

            $PHORUM["DATA"]["OKMSG"] = "This page is not support on the mobile site";
            if($_SERVER["HTTP_REFERER"]){
                $PHORUM["DATA"]["URL"]["CLICKHERE"] = $_SERVER["HTTP_REFERER"];
            } else {
                $PHORUM["DATA"]["URL"]["CLICKHERE"] = $PHORUM["http_path"];
            }
            $PHORUM["DATA"]["CLICKHEREMSG"] = $PHORUM["LANG"]["Back"];
            phorum_output("message");
            exit();
        }

        $PHORUM["use_new_folder_style"] = true;
        $PHORUM["threaded_read"] = 0;
        $PHORUM["threaded_list"] = 0;
        $PHORUM["list_length_flat"] = 10;
        $PHORUM["read_length"] = 10;
        $PHORUM["cache_messages"] = 0;
        $PHORUM["reply_on_read_page"] = 0;
        $PHORUM["long_date_time"] = $PHORUM["short_date_time"];

        if(!empty($PHORUM["hooks"]["after_header"])){
            unset($PHORUM["hooks"]["after_header"]);
        }
        if(!empty($PHORUM["hooks"]["before_footer"])){
            unset($PHORUM["hooks"]["before_footer"]);
        }

        if(phorum_page == "pm"){
            /**
             * create new URL for PM folders page for mobile templates to use
             */
            $PHORUM["DATA"]["MOD_MOBILE"]["URL"]["PM_SHOW_FOLDER_LIST"] = phorum_get_url(PHORUM_PM_URL, "folders=1");

            /**
             * Additionally, check the variable and set a template var if its set
             */
            if(!empty($PHORUM["args"]["folders"])){
                $PHORUM["DATA"]["PM_SHOW_FOLDERS"] = true;
            }
        }

    }
}

?>
