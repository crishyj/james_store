<?php
$title = htmlspecialchars($_GET['title']);
$coverHref = htmlspecialchars($_GET['coverHref']);
$link = htmlspecialchars($_GET['link']);

if (!isset($_GET['link'])){
	exit();
}

echo '<html><head>
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@englishmajorpub" />
<meta name="twitter:title" content="'.$title.'" />
<meta name="twitter:description" content="View ebook on the Embellisher Ereader." />
<meta name="twitter:image" content="'.$coverHref.'" />
<meta name="twitter:url" content="'.$link.'" />
<meta http-equiv="refresh" content="2; url='.$link.'" />
</head>
<body>
You will be redirected to the ebook..
</body>
</html>';