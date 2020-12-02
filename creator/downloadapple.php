<?php
ini_set('max_execution_time', 6000); //6000 seconds = 100 minutes
set_time_limit(6000);
ob_start();
session_start();
include 'config.php';
require_once 'includes/functions.php';
require_once 'includes/ebook_create.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['type']))
{
    header('Location: index.php'); 
    die();
}


if (isset($_GET['book'])){
   $bookid = $DB->real_escape_string($_GET['book']);
}
if (isset($bookid)){
	
	$SEL = "SELECT * FROM private_library WHERE id='$bookid'";
	$rb = $DB->query($SEL);
	if ($B = $rb->fetch_assoc()){
		$booktitle = $B['title'];
		$book = $B;
	}
}
if (isset($book)){
	$the_folder = rtrim($book['rootUrl'],'/');

	generateTOC_apple($DB,$bookid);
	

	$zip_file_name = $book['id'].'.epub';
	$zip_file_name = $SERVERURL.'ebooks/'.str_replace(" ","_",$zip_file_name);


	$download_file= true;
	//$delete_file_after_download= true; doesnt work!!
	
	function createZipFromDir($dir, $zip_file) {
		file_put_contents($zip_file, base64_decode("UEsDBAoAAAAAAOmRAT1vYassFAAAABQAAAAIAAAAbWltZXR5cGVhcHBsaWNhdGlvbi9lcHViK3ppcFBLAQIUAAoAAAAAAOmRAT1vYassFAAAABQAAAAIAAAAAAAAAAAAIAAAAAAAAABtaW1ldHlwZVBLBQYAAAAAAQABADYAAAA6AAAAAAA="));

		$zip = new ZipArchive;

		// open archive
		if (($err = $zip->open($zip_file)) !== TRUE) {
		    return false;
		}

		
		zipDir($dir, $zip);
		return $zip;
	}

	function zipDir($dir, $zip, $relative_path = DIRECTORY_SEPARATOR) {
		$extensions = array(".png",".jpg",".gif",".otf",".ttf",".js",".eot",".woff",".css",".swf",".svg",
	        ".avi",
	        ".bmp",
	        ".mp3",".mpg",".wav",".m3u",".ogg",".oga",".m4a",".webma",
	        ".mp4",".m4v",".ogv",".webm",".xhtml",".xml",".opf",".ncx"
	        );

		$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				$ext = ".".pathinfo($file, PATHINFO_EXTENSION);

				if ($file === '.' || $file === '..' ) {
					continue;
				}
				if (is_file($dir . $file) && in_array($ext,$extensions) ) {
					$zip->addFile($dir . $file, $file);
				} elseif (is_dir($dir . $file)) {
					zipDir($dir . $file, $zip, $relative_path . $file);
				}
			}
		}
		closedir($handle);
	}


	class FlxZipArchive extends ZipArchive {
		/** Add a Dir with Files and Subdirs to the archive;;;;; @param string $location Real Location;;;;  @param string $name Name in Archive;;; @author Nicolas Heimann;;;; @access private  **/

		public function addDir($location, $name) {
			$this->addEmptyDir($name);

			$this->addDirDo($location, $name);
		 } // EO addDir;

		/**  Add Files & Dirs to archive;;;; @param string $location Real Location;  @param string $name Name in Archive;;;;;; @author Nicolas Heimann* @access private   **/
		public function addDirDo($location, $name) {
			if ($name != ""){
				$name .= '/';
			}
			$location .= '/';

			$extensions = array(".png",".jpg",".jpeg",".gif",".otf",".ttf",".js",".eot",".woff",".css",".swf",".svg",
	        ".avi",
	        ".bmp",
	        ".mp3",".mpg",".wav",".m3u",".ogg",".oga",".m4a",".webma",
	        ".mp4",".m4v",".ogv",".webm",".xhtml",".xml",".opf",".ncx"
	        );
	        


			// Read all Files in Dir
			$dir = opendir ($location);
			while ($file = readdir($dir))
			{
				//echo $file ."<br/>";
				$ext = strtolower( ".".pathinfo($file, PATHINFO_EXTENSION) ); 
				if ($file == '.' || $file == '..' || $file == 'mimetype' || $file =='encryption.xml') continue;
				// Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
				$do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
				if ($do == 'addFile' && !in_array($ext,$extensions)){
					continue;
				}else{
					$this->$do($location . $file, $name . $file);
				}
				
			}
		} // EO addDirDo();
	}

	$za = new FlxZipArchive;
	unlink($zip_file_name);
	
	file_put_contents($zip_file_name, base64_decode("UEsDBAoAAAAAAOmRAT1vYassFAAAABQAAAAIAAAAbWltZXR5cGVhcHBsaWNhdGlvbi9lcHViK3ppcFBLAQIUAAoAAAAAAOmRAT1vYassFAAAABQAAAAIAAAAAAAAAAAAIAAAAAAAAABtaW1ldHlwZVBLBQYAAAAAAQABADYAAAA6AAAAAAA="));

	$res = $za->open($zip_file_name);
	if($res === TRUE) 
	{
		$za->addDirDo($the_folder, '');
		$za->close();
	}
	else  { echo 'Could not create a zip archive';}

	if ($download_file)
	{
		ob_get_clean();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/epub+zip");
		header("Content-Disposition: attachment; filename=" . basename($zip_file_name) );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($zip_file_name));
		readfile($zip_file_name);

	}
	generateTOC($DB,$bookid);
}
?>