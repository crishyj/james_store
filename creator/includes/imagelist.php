<?php // this must be the very first line in your PHP file!

// You can't simply echo everything right away because we need to set some headers first!
$output = ''; // Here we buffer the JavaScript code we want to send to the browser.
$delimiter = "\n"; // for eye candy... code gets new lines

$dir = $_GET['dir'];
$directory = "../".$dir; // Use your correct (relative!) path here


$imagelist = array();
$image = array();

if (is_dir($directory)) {
    if ($handle = opendir($directory)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (is_file($directory.$entry) && getimagesize($directory.$entry) != FALSE) {
                    $image['title'] = $entry;
                    $image['value'] = $dir.$entry;
                    $imageList[] = $image;
                }
                
            }
        }
        closedir($handle);
    }
}

    
// Now we can send data to the browser because all headers have been set!
echo json_encode($imageList);

?>