<?php

define('ROOT_URL', 'http://james.com');

//Generate one html file with the conplete book.
function generateOneFile($DB, $bookid, $userid)
{
    //select book from database
    $sel = "SELECT * FROM private_library WHERE userid='$userid' AND id='$bookid'";
    $res = $DB->query($sel);
    if ($B = $res->fetch_assoc()) {
        $filename = 'htmlbook'.$B['id'];
        $fileloc = 'ebooks/'.$filename.'.html';

        //get songs
        $GETSONGS = "SELECT * FROM private_audio WHERE bookid='$bookid'";
        $songs = $DB->query($GETSONGS);
        $song_titles = array();
        $song_files = array();
        while ($song = $songs->fetch_assoc()) {
            $song_titles[] = $song['name'];
            $song_files[] = $song['audiofile'];
        }

        $html = '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <base href="'.ROOT_URL.'/creator/'.$B['rootUrl'].'OPS/text/" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="index, follow" />
  <meta name="keywords" content="" />
  <meta name="title" content="'.$B['title'].'" />
  <meta name="author" content="'.$B['author'].'" />
  <meta name="description" content="Ebook by EMRE publishing Epub 3 creator" />
  <meta name="generator" content="Van Stein en Groentjes Epub Creator" />
  <title>'.$B['title'].'</title>
  <script type="text/javascript" src="../assets/js/jquery.js"></script>
  <script type="text/javascript" src="../assets/js/choicesystem.js"></script>
  <link href="../assets/video-js/video-js.css" rel="stylesheet" />
  <link href="../assets/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src="../assets/video-js/video.js"></script>
  <script type="text/javascript">
    videojs.options.flash.swf = "../assets/video-js/video-js.swf";
  </script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


</head>
<body>


';

        //get chapter nr.
        $logicnumber = 0;
        $GETchapters = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $chapters = $DB->query($GETchapters);
        $nosongsyet = true;
        while ($chap = $chapters->fetch_assoc()) {
            $chap_nr = $logicnumber;
            $chap_id = $chap['id'];
            $sqlsave = "UPDATE private_chapters SET chapter_nr = '$chap_nr' WHERE id='$chap_id'";
            $DB->query($sqlsave);
            $chapc = $chap['content'];
            $chapcontent = str_replace('../', ROOT_URL.'/creator/'.$B['rootUrl'].'OPS/', $chapc);
            $html .= entities_to_unicode($chapcontent).'<br/>';

            ++$logicnumber;
        }
        $html .= '
</body>
</html>
';

        file_put_contents($fileloc, $html);
    }
}

//GENERATE TABLE OF CONTENT toc.xhtml file for book with id bookid.
function generateTOC($DB, $bookid)
{
    $SEL = "SELECT * FROM private_library WHERE id='$bookid'";
    $res = $DB->query($SEL);
    if ($B = $res->fetch_assoc()) {
        $title = htmlspecialchars($B['title']);
        $loc = $B['rootUrl'];
        $ncxcontent = '<?xml version=\'1.0\' encoding=\'utf-8\'?>
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1" xml:lang="en">
    <head>
        <meta content="123-0-1234567-0-'.$B['id'].'" name="dtb:uid"/>
    </head>
    <docTitle>
        <text>'.$title.'</text>
    </docTitle>
    <navMap>
';

        $content = '<?xml version=\'1.0\' encoding=\'UTF-8\' ?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>'.$title.'</title>
    <meta charset="utf-8" />
  </head>
  <body>
    <section epub:type="toc" xmlns:epub="http://www.idpf.org/2007/ops">
      <header>
        <h1>Table of Contents</h1>
      </header>
    </section>
    <nav epub:type="toc" id="toc" xmlns:epub="http://www.idpf.org/2007/ops">
      <ol>
';
        $selchapt = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $res = $DB->query($selchapt);
        while ($c = $res->fetch_assoc()) {
            $content .= '        <li><a href="text/'.$c['chapter_nr'].'-chapter.html">'.htmlspecialchars($c['title']).'</a></li>
';
            $ncxcontent .= '<navPoint id="c'.$c['chapter_nr'].'-chapter" playOrder="'.$c['chapter_nr'].'">
            <navLabel>
                <text>'.$c['title'].'</text>
            </navLabel>
            <content src="text/'.$c['chapter_nr'].'-chapter.html"/>
        </navPoint>
';
        }
        $content .= '
      </ol>
    </nav>
  </body>
</html>';
        $ncxcontent .= '    </navMap>
</ncx>';
        $fileloc = $loc.'OPS/toc.xhtml';
        file_put_contents($fileloc, $content);

        $fileloc = $loc.'OPS/toc.ncx';
        file_put_contents($fileloc, $ncxcontent);
    }
    generateContentOPF($DB, $bookid);
}

//GENERATE TABLE OF CONTENT toc.xhtml file for book with id bookid.
//FOR APPLE ONLY
function generateTOC_apple($DB, $bookid)
{
    $SEL = "SELECT * FROM private_library WHERE id='$bookid'";
    $res = $DB->query($SEL);
    if ($B = $res->fetch_assoc()) {
        $title = htmlspecialchars($B['title']);
        $loc = $B['rootUrl'];
        $ncxcontent = '<?xml version=\'1.0\' encoding=\'utf-8\'?>
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1" xml:lang="en">
    <head>
        <meta content="123-0-1234567-0-'.$B['id'].'" name="dtb:uid"/>
    </head>
    <docTitle>
        <text>'.$title.'</text>
    </docTitle>
    <navMap>
';

        $content = '<?xml version=\'1.0\' encoding=\'UTF-8\' ?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>'.$title.'</title>
    <meta charset="utf-8" />
  </head>
  <body>
    <section epub:type="toc" xmlns:epub="http://www.idpf.org/2007/ops">
      <header>
        <h1>Table of Contents</h1>
      </header>
    </section>
    <nav epub:type="toc" id="toc" xmlns:epub="http://www.idpf.org/2007/ops">
      <ol>
';
        $selchapt = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $res = $DB->query($selchapt);
        while ($c = $res->fetch_assoc()) {
            $content .= '        <li><a href="text/'.$c['chapter_nr'].'-chapter.xhtml">'.htmlspecialchars($c['title']).'</a></li>
';
            $ncxcontent .= '<navPoint id="c'.$c['chapter_nr'].'-chapter" playOrder="'.$c['chapter_nr'].'">
            <navLabel>
                <text>'.$c['title'].'</text>
            </navLabel>
            <content src="text/'.$c['chapter_nr'].'-chapter.xhtml"/>
        </navPoint>
';
        }
        $content .= '
      </ol>
    </nav>
  </body>
</html>';
        $ncxcontent .= '    </navMap>
</ncx>';
        $fileloc = $loc.'OPS/toc.xhtml';
        file_put_contents($fileloc, $content);

        $fileloc = $loc.'OPS/toc.ncx';
        file_put_contents($fileloc, $ncxcontent);
    }
    generateContentOPF_apple($DB, $bookid);
}

function endsWith($haystack, $needle)
{
    // search forward starting from end minus needle length characters
    return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

/**
 * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively.
 * Will return an array of the form
 * array(
 *   'files' => [],
 *   'dirs'  => [],
 * ).
 *
 * @author sreekumar
 *
 * @param string $root
 * @result array
 */
function read_all_files($root = '.', $extension = '.php')
{
    set_time_limit(600);
    $files = array('files' => array(), 'dirs' => array());
    $directories = array();
    $last_letter = $root[strlen($root) - 1];
    $root = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;

    $directories[] = $root;

    while (sizeof($directories)) {
        $dir = array_pop($directories);
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $file = $dir.$file;
                if (is_dir($file)) {
                    $directory_path = $file.DIRECTORY_SEPARATOR;
                    array_push($directories, $directory_path);
                    $files['dirs'][] = $directory_path;
                } elseif (is_file($file) && endsWith(strtolower($file), strtolower($extension))) {
                    $files['files'][] = $file;
                }
            }
            closedir($handle);
        }
    }

    return $files;
}

function generateContentOPF($DB, $bookid)
{
    $SEL = "SELECT * FROM private_library WHERE id='$bookid'";
    $res = $DB->query($SEL);
    if ($B = $res->fetch_assoc()) {
        $title = htmlspecialchars($B['title']);
        $author = htmlspecialchars($B['author']);
        $lang = htmlspecialchars($B['lang']);
        $pagination = htmlspecialchars($B['pagination']);
        $loc = $B['rootUrl'];
        $coverloc = substr($B['coverHref'], strrpos($B['coverHref'], '/'));

        $coverloc = 'img'.$coverloc;

        $content = '<?xml version="1.0"  encoding="UTF-8"?>
<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="uuid" version="3.0">
  <metadata xmlns:opf="http://www.idpf.org/2007/opf" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <dc:creator>'.$author.'</dc:creator>
    <dc:title>'.$title.'</dc:title>
    <dc:identifier id="uuid">123-0-1234567-0-'.$B['id'].'</dc:identifier>
    <meta property="dcterms:modified">'.date('Y-m-d').'T12:00:00Z</meta>
    <meta name="cover" content="'.$coverloc.'"/>
    <dc:language>'.$lang.'</dc:language>
</metadata>
  <manifest>

    <!-- TOC -->
    <item href="toc.xhtml" id="toc-xhtml" media-type="application/xhtml+xml" properties="nav"/>
    <item href="toc.ncx" id="toc-ncf" media-type="application/x-dtbncx+xml"/>

    <!-- Readers -->
';
        $selchapt = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $res = $DB->query($selchapt);
        while ($c = $res->fetch_assoc()) {
            $content .= '    <item href="text/'.$c['chapter_nr'].'-chapter.html" id="chapter'.$c['chapter_nr'].'" media-type="text/html" fallback="chapter'.$c['chapter_nr'].'x"/>
';
            $content .= '    <item href="text/'.$c['chapter_nr'].'-chapter.xhtml" id="chapter'.$c['chapter_nr'].'x" media-type="text/html"/>
';
        }

        $extensions = array('.png', '.jpg', '.jpeg', '.gif', '.otf', '.ttf', '.js', '.eot', '.woff', '.css', '.swf', '.svg',
      '.avi',
      '.bmp',
      '.mp3', '.mpg', '.wav', '.m3u', '.ogg', '.oga', '.m4a', '.webma',
      '.mp4', '.m4v', '.ogv', '.webm',
    );
        $mediatype = array('image/png', 'image/jpg', 'image/jpg', 'image/gif', 'application/octet-stream', 'application/octet-stream', 'text/javascript', 'application/octet-stream', 'application/octet-stream', 'text/css', 'application/octet-stream', 'application/octet-stream',
      'video/avi',
      'mage/bmp',
      'audio/mpeg', 'audio/mpeg', 'audio/wav', 'audio/x-mpegurl', 'audio/ogg', 'audio/ogg', 'audio/mp4', 'audio/webm',
      'video/mp4', 'video/ogg', 'video/webm',
    );

        //run trough all files to get images:

        $all_files = read_all_files(''.$B['rootUrl'], '');

        $id_counter = 0;
        $extension_count = count($extensions);
        foreach ($all_files['files'] as $f) {
            for ($i = 0; $i < $extension_count; ++$i) {
                $f = str_replace($B['rootUrl'].'OPS/', '', $f);
                if (endsWith(strtolower($f), strtolower($extensions[$i]))) {
                    $content .= '
    <item href="'.$f.'" id="unit-'.$id_counter.'" media-type="'.$mediatype[$i].'"/>';
                    ++$id_counter;
                }
            }
        }

        $content .= '


  </manifest>
  <spine page-progression-direction="'.$pagination.'" toc="toc-ncf">
  ';
        $selchapt = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $res = $DB->query($selchapt);
        while ($c = $res->fetch_assoc()) {
            $content .= '    <itemref idref="chapter'.$c['chapter_nr'].'" />
';
        }
        $content .= '  </spine>
</package>';

        $fileloc = $loc.'OPS/content.opf';
        file_put_contents($fileloc, $content);
    }
}

//Apple unit to generate the opf.
function generateContentOPF_apple($DB, $bookid)
{
    $SEL = "SELECT * FROM private_library WHERE id='$bookid'";
    $res = $DB->query($SEL);
    if ($B = $res->fetch_assoc()) {
        $title = htmlspecialchars($B['title']);
        $author = htmlspecialchars($B['author']);
        $lang = htmlspecialchars($B['lang']);
        $pagination = htmlspecialchars($B['pagination']);
        $loc = $B['rootUrl'];
        $coverloc = substr($B['coverHref'], strrpos($B['coverHref'], '/'));

        $coverloc = 'img'.$coverloc;

        $content = '<?xml version="1.0"  encoding="UTF-8"?>
<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="uuid" version="3.0">
  <metadata xmlns:opf="http://www.idpf.org/2007/opf" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <dc:creator>'.$author.'</dc:creator>
    <dc:title>'.$title.'</dc:title>
    <dc:identifier id="uuid">123-0-1234567-0-'.$B['id'].'</dc:identifier>
    <meta property="dcterms:modified">'.date('Y-m-d').'T12:00:00Z</meta>
    <meta name="cover" content="'.$coverloc.'"/>
    <dc:language>'.$lang.'</dc:language>
</metadata>
  <manifest>

    <!-- TOC -->
    <item href="toc.xhtml" id="toc-xhtml" media-type="application/xhtml+xml" properties="nav"/>
    <item href="toc.ncx" id="toc-ncf" media-type="application/x-dtbncx+xml"/>

    <!-- Readers -->
';
        $extensions = array('.png', '.jpg', '.jpeg', '.gif', '.otf', '.ttf', '.js', '.eot', '.woff', '.css', '.swf', '.svg',
      '.avi',
      '.bmp',
      '.mp3', '.mpg', '.wav', '.m3u', 'ogg', 'oga', 'm4a', 'webma',
      '.mp4', '.m4v', '.ogv', 'webm',
    );
        $mediatype = array('image/png', 'image/jpeg', 'image/jpeg', 'image/gif', 'application/octet-stream', 'application/octet-stream', 'text/javascript', 'application/octet-stream', 'application/octet-stream', 'text/css', 'application/octet-stream', 'application/octet-stream',
      'video/avi',
      'mage/bmp',
      'audio/mpeg', 'audio/mpeg', 'audio/wav', 'audio/x-mpegurl', 'audio/ogg', 'audio/ogg', 'audio/mp4', 'audio/webm',
      'video/mp4', 'video/ogg', 'video/webm',
    );
        $selchapt = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $res = $DB->query($selchapt);
        $linknr = 0;
        while ($c = $res->fetch_assoc()) {
            $remote_sources = '';
            // remote-resources
            $chaptercontent = $c['content'];
            $chaptercontent = str_replace($APP_LOC.'/'.$booklocation.'OPS/', '../', $chaptercontent);
            $chaptercontent = str_replace(ROOT_URL, '', $chaptercontent);
            $chaptercontent = str_replace(ROOT_URL, '', $chaptercontent);
            //check for outside resources
            //src="http

            //echo $regexp;
            //echo "<br/>".$chaptercontent;
            //$content .= "<!-- We try to find a match for  /\s+[^>]*?src=(\"|')([^\"']+)\1/-->";
            if (preg_match_all("/src=(\"|')http([^\"']+)(\"|')/", $chaptercontent, $matches)) {
                // $matches[2] = array of link addresses
                // $matches[3] = array of link text - including HTML code

                foreach ($matches[2] as $m) {
                    $remote_sources = ' remote-resources';

                    ++$linknr;
                    $found = 0;
                    for ($i = 0; $i < $extension_count; ++$i) {
                        if (endsWith(strtolower($m), strtolower($extensions[$i]))) {
                            //<item id="vid" href="http://www.elizabethcastro.com/epub/examples/catbox.mp4" media-type="video/quicktime" />
                            $content .= '    <item href="'.$m.'" id="link'.$linknr.'" media-type="'.$mediatype[$i].'"/>
';
                            ++$found;
                        }
                    }
                    if ($found == 0) {
                        //add it as html
                        $content .= '    <item href="'.$m.'" id="link'.$linknr.'" media-type="text/html"/>
';
                    }
                }
            }
            $content .= '    <item href="text/'.$c['chapter_nr'].'-chapter.xhtml" id="chapter'.$c['chapter_nr'].'" media-type="application/xhtml+xml" properties="scripted'.$remote_sources.'"/>
';
        }

        //run trough all files to get images:

        $all_files = read_all_files(''.$B['rootUrl'], '');

        $id_counter = 0;
        $extension_count = count($extensions);
        foreach ($all_files['files'] as $f) {
            for ($i = 0; $i < $extension_count; ++$i) {
                $f = str_replace($B['rootUrl'].'OPS/', '', $f);
                if (endsWith(strtolower($f), strtolower($extensions[$i]))) {
                    $escaped_f = str_replace(' ', '%20', $f);
                    $content .= '
    <item href="'.$escaped_f.'" id="unit-'.$id_counter.'" media-type="'.$mediatype[$i].'"/>';
                    ++$id_counter;
                }
            }
        }

        $content .= '


  </manifest>
  <spine page-progression-direction="'.$pagination.'" toc="toc-ncf">
  ';
        $selchapt = "SELECT * FROM private_chapters WHERE bookid='$bookid' ORDER BY chapter_nr ASC";
        $res = $DB->query($selchapt);
        while ($c = $res->fetch_assoc()) {
            $content .= '    <itemref idref="chapter'.$c['chapter_nr'].'" />
';
        }
        $content .= '  </spine>
</package>';

        $fileloc = $loc.'OPS/content.opf';
        file_put_contents($fileloc, $content);
    }
}

function entities_to_unicode($str)
{
    $str = str_replace('&lt', '--SPECIALSYM123--', $str);
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    $str = preg_replace_callback('/(&#[0-9]+;)/', function ($m) { return mb_convert_encoding($m[1], 'UTF-8', 'HTML-ENTITIES'); }, $str);
    $str = str_replace('--SPECIALSYM123--', '&lt', $str);

    return $str;
}

//writes the content and audio playlist of a chapter to a file.
function writeChapterToFile($chapter_content, $chapter_title, $chapter_nr, $chapter_extraHeaders, $booklocation, $song_titles = array(), $song_files = array(), $video_titles = array(), $video_files = array(), $APP_LOC = '/creator')
{
    $html = '<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="../assets/css/styles.css" />
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/choicesystem.js"></script>
        <link href="../assets/video-js/video-js.css" rel="stylesheet"/>
        <script src="../assets/video-js/video.js"></script>
        <script>
          videojs.options.flash.swf = "../assets/video-js/video-js.swf"
        </script>';

    $html .= '
        <title>'.$chapter_title.'</title>
        '.$chapter_extraHeaders.'
    </head>
    <body>';
    if (count($song_titles) > 0) {
        $html .= '<div id="ee-soundtrack" style="display:none">';
        for ($i = 0; $i < count($song_titles); ++$i) {
            $audiofile = str_replace($APP_LOC.'/'.$booklocation.'OPS/', '../', $song_files[$i]);

            $audiofile = str_replace(ROOT_URL, '', $audiofile);
            // $html .= '<div name="track-'.$i.'" src="'.$audiofile.'" recommended="no">'.$song_titles[$i].'</div>';
            $html .= '<div name="track-'.$i.'" src="'.$song_files[$i].'" recommended="no">'.$song_titles[$i].'</div>';
        }
        $html .= '</div>';
    }

    if (count($video_titles) > 0) {
        $html .= '<div id="ee-videotrack" style="display:none">';
        for ($i = 0; $i < count($video_titles); ++$i) {
            $videofile = str_replace($APP_LOC.'/'.$booklocation.'OPS/', '../', $video_files[$i]);
            $videofile = str_replace(ROOT_URL, '', $videofile);
            // $html .= '<div name="videotrack-'.$i.'" src="'.$videofile.'" recommended="no">'.$video_titles[$i].'</div>';
            $html .= '<div name="videotrack-'.$i.'" src="'.$video_files[$i].'" recommended="no">'.$video_titles[$i].'</div>';
        }
        $html .= '</div>';
    }

    $nicehtml = $chapter_content;
    $nicehtml = str_replace('id="-', 'id="a', $nicehtml);
    $nicehtml = str_replace('href="#-', 'href="#a', $nicehtml);
    $html .= entities_to_unicode($nicehtml).'
    </body>
</html>';

    $xhtml = '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />
        <link type="text/css" rel="stylesheet" href="../assets/css/styles.css"></link>
        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/js/choicesystem.js"></script>
        <link href="../assets/video-js/video-js.css" rel="stylesheet"></link>
        <script src="../assets/video-js/video.js"></script>
        <script>
          videojs.options.flash.swf = "../assets/video-js/video-js.swf"
        </script>';

    $xhtml .= '
        <title>'.$chapter_title.'</title>
        '.$chapter_extraHeaders.'
    </head>
    <body>';
    /*if (count($song_titles)>0){
          $xhtml .=  '<div id="ee-soundtrack" style="display:none;">';
           for ($i=0; $i< count($song_titles); $i++){
              $audiofile = str_replace($APP_LOC.'/'.$booklocation.'OPS/',"../",$song_files[$i]);
              $audiofile = str_replace("https://emrepublishing.com","",$audiofile);
              $xhtml .= '<div name="track-'.$i.'" src="'.$audiofile.'" recommended="no">'.$song_titles[$i].'</div>';
          }
          $xhtml .=  '</div>';
      }*/

    // $tidy_config = array(
    //   'clean' => false,
    //   'output-xhtml' => true,
    //   'show-body-only' => true,
    //   'wrap' => 0,

    // );
    // $tidy = new tidy();
    // $tidy = tidy_parse_string( $chapter_content, $tidy_config, 'UTF8' );
    // $tidy->cleanRepair();
    // $tidy = str_replace( "&nbsp;", " ", $tidy );
    // $tidy = str_replace( "id=\"-", "id=\"a", $tidy );
    // $tidy = str_replace( "href=\"#-", "href=\"#a", $tidy );
    // //href="#-
    // //frameborder="0" allowfullscreen="allowfullscreen"
    // $tidy = str_replace( "frameborder=\"0\"", "", $tidy );
    // $tidy = str_replace( "allowfullscreen=\"allowfullscreen\"", "", $tidy );
    // $tidy = str_replace( "&", "&amp;", $tidy );
    // $tidy = str_replace( "&amp;amp;", "&amp;", $tidy );
    // $xhtml .=  $tidy.
    '
    </body>
</html>';

    if (!file_exists($booklocation.'OPS/text')) {
        mkdir($booklocation.'OPS/text', 0755, true);
    }

    $filename = 'chapter';
    $fileloc = $booklocation.'OPS/text/'.$chapter_nr.'-'.$filename.'.html';
    $fileloc2 = $booklocation.'OPS/text/'.$chapter_nr.'-'.$filename.'.xhtml';
    file_put_contents($fileloc, $html);
    file_put_contents($fileloc2, $xhtml);
}

//adds a front page to the ebook
function insertCoverPage($bookid, $coverloc, $DB)
{
    $htmlori = '<img src="'.$coverloc.'" class="coverimage"/>';
    $html = $DB->real_escape_string($htmlori);
    $INS = "INSERT INTO private_chapters (bookid,chapter_nr,title,content) VALUES ('$bookid','0','Cover','$html')";
    //echo $INS;
    $DB->query($INS);

    return $htmlori;
}

function ChangeBook($bookid, $userid, $booktitle, $author, $files, $server, $DB)
{
    $SERVERURL = $server;

    if ($files['coverimage']['name']) {
        $filename = $files['coverimage']['name'];
        $filename = str_replace(' ', '_', $filename);
        $source = $files['coverimage']['tmp_name'];
        $type = $files['coverimage']['type'];

        $info = getimagesize($files['coverimage']['tmp_name']);
        $okay = true;
        if ($info === false) {
            $okay = false;
        }

        // $continue = strtolower($name[1]) == 'zip' ? true : false;
        if (!$okay) {
            $err = 'The cover image file you are trying to upload is not an image. Please try again.';
        } else {
            /* PHP current path */
            $duplicatecounter = 0;
            $targetdir = 'coverimages/'.$duplicatecounter;
            $originaltarget = 'coverimages/';

            while (is_dir($targetdir)) {
                $targetdir = $originaltarget.$duplicatecounter;
                ++$duplicatecounter;
            } //rmdir_recursive ( $targetdir);

            mkdir($targetdir, 0755);

            /* here it is really happening */

            if (move_uploaded_file($source, $targetdir.'/'.$filename)) {
                $coverlocation = $SERVERURL.$targetdir.'/'.$filename;
            } else {
                $err = 'There was a problem with the upload of the coverimage. Please try again.';
                echo $err;
                exit();
            }

            //Also move the cover to the epub folder
            $coverforcontent = '../img/'.$filename;
            $coverloc = $newbooklocation.'OPS/img/'.$filename;
            copy($targetdir.'/'.$filename, $coverloc);
        }
    }

    //save ebook to server
    $UPD = "UPDATE private_library SET title='$booktitle',author='$author' where userid ='$userid' AND id='$bookid'";
    if (isset($coverlocation) && $coverlocation != '') {
        $UPD = "UPDATE private_library SET title='$booktitle',author='$author', coverHref='$coverlocation' where userid ='$userid' AND id='$bookid'";
    }
    //echo $INS;
    if ($DB->query($UPD)) {
        return true;
    }

    return false;
}

//Function to create an empty ebook
//provide a userid, the title of the book, the author, the array with coverimage as file
//serverurl and the database object
//template number
function CreateNew($userid, $booktitle, $author, $files, $server, $DB, $template, $lang, $pagination)
{
    $SERVERURL = $server;

    $templatelocations = array('ebooks/empty/', 'ebooks/family/', 'ebooks/child/', 'ebooks/castle/');

    $newbooklocation = 'ebooks/'.$userid.'b'.generateRandomString(8).'/';

    $emptybooklocation = $templatelocations[$template];

    while (is_dir($newbooklocation)) {
        $newbooklocation = 'ebooks/'.$userid.'b'.generateRandomString(8).'/';
    }
    //move empty book to new book location
    xcopy($emptybooklocation, $newbooklocation);

    $locations = array();

    if ($files['coverimage']['name']) {
        $filename = $files['coverimage']['name'];
        $filename = str_replace(' ', '_', $filename);
        $source = $files['coverimage']['tmp_name'];
        $type = $files['coverimage']['type'];

        $info = getimagesize($files['coverimage']['tmp_name']);
        $okay = true;
        if ($info === false) {
            $okay = false;
        }

        // $continue = strtolower($name[1]) == 'zip' ? true : false;
        if (!$okay) {
            $err = 'The cover image file you are trying to upload is not an image. Please try again.';
        } else {
            /* PHP current path */
            $duplicatecounter = 0;
            $targetdir = 'coverimages/'.$duplicatecounter;
            $originaltarget = 'coverimages/';

            while (is_dir($targetdir)) {
                $targetdir = $originaltarget.$duplicatecounter;
                ++$duplicatecounter;
            } //rmdir_recursive ( $targetdir);

            mkdir($targetdir, 0755);

            /* here it is really happening */

            if (move_uploaded_file($source, $targetdir.'/'.$filename)) {
                $coverlocation = $SERVERURL.$targetdir.'/'.$filename;
            } else {
                $err = 'There was a problem with the upload of the coverimage. Please try again.';
                echo $err;
                exit();
            }

            //Also move the cover to the epub folder
            $coverforcontent = '../img/'.$filename;
            $coverloc = $newbooklocation.'OPS/img/'.$filename;
            copy($targetdir.'/'.$filename, $coverloc);
        }
    }

    $locations['cover'] = $coverlocation;
    $locations['book'] = $newbooklocation;

    $packageurl = $DB->real_escape_string('OPS/content.opf');
    $newbooklocation = $DB->real_escape_string($locations['book']);
    $coverlocation = $DB->real_escape_string($locations['cover']);
    //save ebook to server
    $INS = "INSERT INTO private_library (title,author,userid,packagePath,rootUrl,coverHref,template, lang, pagination) VALUES ('$booktitle','$author','$userid','$packageurl','$newbooklocation','$coverlocation','$template', '$lang','$pagination')";

    //echo $INS;
    if ($DB->query($INS) !== true) {
        echo 'Error: '.$INS.'<br>'.$DB->error;
    }

    $bookid = $DB->insert_id;
    $covercontent = insertCoverPage($bookid, $coverforcontent, $DB);
    writeChapterToFile($covercontent, 'Cover', '0', '', $newbooklocation);

    generateTOC($DB, $bookid);

    //$newbooklocation = $DB->real_escape_string( $newbooklocation);
    return $bookid;
}
