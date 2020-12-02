<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 1000');
require('config.php');
//$session = $DB->escape_string( $_POST["sessionid"] );
$username = $DB->escape_string( $_GET["email"] );

$search = "";
if (isset($_POST["search"])){
	$search = $DB->escape_string( $_POST["search"] );
}
$ios = "";
if (isset($_GET["ios"])){
	$ios = $DB->escape_string( $_GET["ios"] );
	if ($ios == 1){
		$ios = "AND (price='' OR price='Free')";
	}
}
$result = array();
$allfree = 0;
$maxresults = 100;


function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function getExcert($bookpath){
	$dom = new DOMDocument();
	$got_excerpt = false;
	$count = 0;

	$exerpt = "";

	$path = $bookpath.'/OPS/text/';
	$path = str_replace(SERVERURL,"../",$path);
	$path = str_replace(CREATORURL,CREATORRELURL,$path);

	echo $path."<br/>";
	if ($handle = opendir($path)){
		echo "INSIDE DIR<br/>";
		while (false !== ($entry = readdir($handle)) && $got_excerpt == false ) {
			
			if (endsWith($entry,".html") || endsWith($entry,".xhtml") && $count > 2 ){
				//open file
				$count ++;
				$fpath = $path . $entry;
				$got_excerpt = true;
			}
		}
	}else{
		
		$path = $bookpath.'/OEBPS/Text/';
		$path = str_replace(SERVERURL,"../",$path);
		$path = str_replace(CREATORURL,CREATORRELURL,$path);
		echo $path."<br/>";
		if ($handle = opendir($path)){
			echo "INSIDE DIR<br/>";
			while (false !== ($entry = readdir($handle)) && $got_excerpt == false ) {
				if (endsWith($entry,".html") || endsWith($entry,".xhtml") ){
					//open file
					$fpath = $path . $entry;
					$got_excerpt = true;
				}
			}
		}else{
			$path = $bookpath.'/';
			$path = str_replace(SERVERURL,"../",$path);
			$path = str_replace(CREATORURL,CREATORRELURL,$path);
			echo $path."<br/>";
			if ($handle = opendir($path)){
				echo "INSIDE DIR<br/>";
				
				while (false !== ($entry = readdir($handle)) && $got_excerpt == false && $count > 2) {
					if (endsWith($entry,".html") || endsWith($entry,".xhtml") ){
						$count ++;
						//open file
						$fpath = $path . $entry;
						$got_excerpt = true;
					}
				}
			}
		}
	}
	echo $fpath;
	if ($got_excerpt == true){
		// Load the url's contents into the DOM
    	$dom->loadHTMLFile($fpath);
    	$xpath = new DOMXPath($dom);
    	$ps = $xpath->query('//h1 | //h2 | //p');

    	foreach ($ps as $p){
    		$exerpt = $exerpt . "<br/>" . $p->textContent;
    		if (strlen($exerpt > 300)){
    			break;
    		}
    	} 
	}
	return $exerpt;

}


$SQL = "SELECT userid, user.interests as interests, allfree FROM user,sessions WHERE email='$username' AND user.id = sessions.userid ";
$RES = $DB->query($SQL);
if ($user = $RES->fetch_assoc()){
	//TODO: get real result from database
	$userid = $user['userid'];
	$interests = $user['interests'];

	$allfree = $user['allfree'];
	
	$retunedbooks = 0;
	if ($search!= ""){
		//check if we search for an id
		$selectSQL = "SELECT * FROM library WHERE  id = '$search' AND id NOT IN (SELECT libraryid FROM user_library WHERE userid='$userid') $ios LIMIT $maxresults";
		$resbookid = $DB->query($selectSQL);

		while ($library_item = $resbookid->fetch_assoc() ){
			$library_item['excerpt'] = getExcert($library_item['rootUrl']);
			$library_item['rootUrl'] = "";

			if ($allfree==1){
				$library_item['price'] = "Free";
			}

			$result[] = $library_item;
			$retunedbooks++;
		}
		if ($retunedbooks == 0){
			//no id found yet
			$selectSQL = "SELECT * FROM library WHERE (title LIKE '%$search%' OR author LIKE '%$search%' OR genre LIKE '%$search%' OR id = '$search') $ios AND id NOT IN (SELECT libraryid FROM user_library WHERE userid='$userid') LIMIT $maxresults";
			$resbook = $DB->query($selectSQL);
			while ($library_item = $resbook->fetch_assoc() ){
				$library_item['excerpt'] = getExcert($library_item['rootUrl']);
				$library_item['rootUrl'] = "";
				if ($allfree==1){
					$library_item['price'] = "Free";
				}
				$result[] = $library_item;
			}
		}
	}else{
		$selectSQL = "SELECT * FROM library WHERE genre LIKE '%$interests%' AND id NOT IN (SELECT libraryid FROM user_library WHERE userid='$userid') $ios LIMIT $maxresults";
		$resbook = $DB->query($selectSQL);
		while ($library_item = $resbook->fetch_assoc() ){
			$library_item['excerpt'] = getExcert($library_item['rootUrl']);
			$library_item['rootUrl'] = "";
			if ($allfree==1){
				$library_item['price'] = "Free";
			}
			$result[] = $library_item;
			$retunedbooks++;
		}
		if ($retunedbooks < $maxresults){
			$remaining = $maxresults - $retunedbooks;
			$selectSQL = "SELECT * FROM library WHERE genre NOT LIKE '%$interests%' AND id NOT IN (SELECT libraryid FROM user_library WHERE userid='$userid') $ios LIMIT $remaining";
			$resbook = $DB->query($selectSQL);
			while ($library_item = $resbook->fetch_assoc() ){
				$library_item['excerpt'] = getExcert($library_item['rootUrl']);
				$library_item['rootUrl'] = "";
				if ($allfree==1){
					$library_item['price'] = "Free";
				}
				$result[] = $library_item;
			}
		}
	}
	

}else{
	//user not logged in!
	$result['error'] = "LOGIN";
}
echo json_encode($result);
