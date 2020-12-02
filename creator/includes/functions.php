<?php
/** 
 * Take a look at the Remark-Lines of the function-header 
 *  
 * function autoAdjustToXHMTL($string) 
 *  
 * Tries to make out of the given string a xhtml-compatible string, 
 * that means that the return-string could be wrapped within a xml. 
 * 
 * Example: $string = "<big><I>something<br></big> blabla" 
 *          return = "<big><i>something><br /></i></big> blabla" 
 * 
 * As you can see, the folowing will be changed: 
 *  - converted "<I>" to "<i>", as all html-nodes will be converted to lowercase 
 *  - converted "<br>" to "<br />" to make br xhtml-conform (same would be done with "img") 
 *  - the closing of "</big>" will have do an implicite closing of "<i>" because <big> surrounds "<i>" 
 * 
 * Beware: the output-string is not the most beautiful code possible but hopefully xhtml-conform 
 * 
 * @param string: input string, html-style (as a browser should like it) 
 * @return: xhtml-compatible string 
 * 
 * Version 
 * V1.0, 20090721, rene@pilz.cc, initial-version 
 * 
 * Released under the terms of BSD (http://en.wikipedia.org/wiki/BSD_licenses) 
 */ 
function autoAdjustToXHTML($string) { 
    $returnString=""; 
    $string=trim($string); 
    $elementStack=array(); 
    $stackPoint=0; 
    while ($string!="") { 
        $pos=strpos($string,"<"); 
        if ($pos===false) { 
            // no "<" found, return full string and exit while 
            $returnString.=$string;     
            break; 
        } 

        // copy anythink up to the "<" into return string and reduce string 
        $returnString.=substr($string,0,$pos); 
        $string=trim(substr($string,$pos+1)); 

        $tagName=""; 
        // examine tag-name 
        $i=0; $c=""; 
        for (;$i<strlen($string);$i++) { 
            $c=substr($string,$i,1); 
            if (strpos(";<",$c)!==false) continue;    // some chars we ignore 
            if ($c==" "|| $c==">") break; 
            $tagName.=$c; 
        } 

        $tagName=strtolower($tagName);    // convert uppercase  

        // is there a closing tag? 
        if (substr($tagName,0,1)=="/") { 
            // search for the closing ">" and ignore all before 
            $pos=strpos($string,">"); 
            if ($pos===false) $pos=strlen($string); 
            $string=substr($string,$pos+1); 
            // close as many tags up to the given tag 
            while ($stackPoint>0) { 
                $stackPoint--; 
                $stackElement=$elementStack[$stackPoint]; 
                $returnString.="</".$stackElement.">"; 
                if ($stackElement==$tagName) break;    // nothing more to do in this while 
            } 
            continue;    // this element-Processing is finished so far, continue at 'while ($string!="") {' 
        } 

        // if we are here, we are within a tag (opening tag) 
         
        // Push tag on stack 
        $elementStack[$stackPoint]=$tagName; 
        $stackPoint++; 

        // add tag to returnString 
        $returnString.="<$tagName "; 
     
        // search up to ">" 
        $inApo=false;    // within Apostrophes (" or ') 
        $fakeApo=false; 
        $apoChar=""; 
        for (;$i<strlen($string);$i++) { 
            $c=substr($string,$i,1); 
            if ($fakeApo && strpos(" \t",$c)) { 
                $fakeApo=false; 
                $returnString.='"'; 
            } 
            if (!$inApo && !$fakeApo && $c=='=' && !strpos(" \t'\"",substr($string,$i+1,1))) { 
                echo "test2=".substr($string,$i+1,1)."---"; 
                $fakeApo=true; 
                $returnString.='="'; 
                $c=""; 
            } 
            if ($c==$apoChar && $inApo) { $inApo=false; $returnString.=$c." "; $c=""; } 
            if ($inApo && $c=="&" && strpos(substr($string,$i).";",";")>5) $c="&amp;"; 
            else if (($c=="'" || $c=='"') && !$inApo) { $inApo=true; $apoChar=$c; } 
            if ($c==">") break; 
            $returnString.=$c; 
        } 

        // new $string is the rest 
        $string=substr($string,$i+1); 
        $returnString=trim($returnString); 
         
        // check if this has a "/>" at the end 
        $endSlash=(substr($returnString,strlen($returnString)-1)=="/"); 

        // some elements must have a "/" at the end --> Fake it if needed 
        if (($tagName=="br" || $tagName=="img") && !$endSlash) {  
            $returnString.="/";  
            $endSlash=true; 
        } 
         
        // check if it is a remark-line (<!-- --> trade this as with-endSlash-Tag) 
        if (substr($tagName,0,3)=="!--") { 
            $returnString=trim($returnString); 
            if (substr($returnString,strlen($returnString)-2)!="--") $returnString.=" --"; // make sure remark-line ends with "-->" 
            $endSlash=true; 
        } 

        // again, do we have a end-slash? (or a faked one?) 
        if ($endSlash) $stackPoint--;    // just remove element from stack 
         
        // and now add the closing ">" 
        $returnString.=">"; 
    } 

    // ok, we are allmost finish, just clean up the elementStack (from last to first) 
    while ($stackPoint>0) { 
        $stackPoint--; 
        $returnString.="</".$elementStack[$stackPoint].">"; 
    } 
    return $returnString; 
} 



/**
 * Copy a file, or recursively copy a folder and its contents
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       string   $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */
function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}

function rmdir_recursive($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
     
    rmdir($dir);
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

