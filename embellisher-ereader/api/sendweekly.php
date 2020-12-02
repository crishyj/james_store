<?php


require('config.php');


if (!isset($_GET["name"]) || $_GET["name"]!="SG"){
	exit();
}

$WeeklyMail = '<html>
<head></head>
<body topmargin="0" leftmargin="0" rightmargin="0">
<div align="center">
You received this email because you registered for the Embellisher Ereader application, if you wish to not receive any more emails, please <a href="https://emrepublishing.com/embellisher-ereader/api/unsubscribe.php?email=##EMAIL_PLACEHOLDER##"> click here </a><br/>
  <table class="body" style="background-color:#ffffff;" bgcolor="#ffffff" border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top" rowspan="1" colspan="1" align="center"><table class="TemplateWidth" style="width:800px;" border="0" width="600" cellspacing="0" cellpadding="1">
          <tr>
            <td valign="top" width="100%" rowspan="1" colspan="1" align="left"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                      <td rowspan="1" colspan="1" align="center"><span /></td>
                    </tr>
                  </table></td>
          </tr>
          <tr>
            <td class="MainBorder" style="background-color:#000000;padding:2px;" bgcolor="#000000" valign="top" rowspan="1" colspan="1" align="left"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="TemplatePad" style="padding:0px;" valign="top" width="800" rowspan="1" colspan="1" align="left"><table style="background-color: #000000;" bgcolor="#000000" width="100%" cellspacing="0" cellpadding="0" id="content_LETTER.BLOCK2"><tr><td style="color: #ffffff; text-align: center; font-size:20px;" width="211" rowspan="1" colspan="1" align="center">&nbsp;<strong>New Ebooks!<br /><br />In the Embellisher Ereader Store.</strong></td><td style="color: #ffffff; text-align: center;" width="200" rowspan="1" colspan="1" align="center">
<div style="text-align: center;" align="center">
<table class="imgCaptionTable OneColumnMobile" style="text-align: right;" width="389" data-padding-converted="true" cellspacing="0" cellpadding="0" align="right"><tr><td class=" imgCaptionImg" style="padding-top: 20px; padding-right: 20px; padding-left: 0px; color: #ffffff;" width="1%" rowspan="1" colspan="1">
<div align="right"><a class="imgCaptionAnchor" track="on" shape="rect" href="https://emrepublishing.com/embellisher-ereader/" target="_blank"><img height="120" vspace="0" border="0" name="ACCOUNT.IMAGE.8" hspace="0" width="120" src="https://mlsvc01-prod.s3.amazonaws.com/8001c1cd401/3f327416-32e2-43a6-aad8-f5e0d4b7fa03.png" /></a></div>
</td></tr><tr><td class="imgCaptionText" style="padding-bottom: 20px; text-align: center; font-style: normal; font-weight: normal; color: #ffffff; font-family: Times New Roman;" rowspan="1" colspan="1"></td></tr></table>
</div>
</td></tr></table></td>
                </tr>
                <tr>
                  <td class="MainBG" style="background-color:#ffffff;" bgcolor="#ffffff" valign="top" width="600" rowspan="1" colspan="1" align="left"><table class="MainText" style="color:#000000;font-family:Arial,Helvetica,sans-serif;font-size:10pt;" border="0" width="100%" cellpadding="15" id="content_LETTER.BLOCK3"><tr><td style="color: #000000;" valign="top" width="70%" rowspan="1" colspan="1" align="left"><b>Dear ##NAME_PLACEHOLDER##,</b>
<div>Check out these new ebooks from the Embellisher Ereader Store!</div>
<div>&nbsp;</div>
</td></tr></table>';

$GET_BOOKS = "SELECT * FROM library ORDER BY id DESC LIMIT 6";
$BOOKS = $DB->query($GET_BOOKS);
$even = false;
while ($B = $BOOKS->fetch_assoc()){
	$WeeklyMail .= '<div style="float:left;width:45%;margin-left:5%; margin-bottom:10px;height:260px;overflow:hidden;">
	<p style="font-size:18px;font-weight:bold;">'.htmlentities($B['title']).'</p>
    <p style="font-size:16px;color:#444;">'.htmlentities($B['author']).'</p>
    <div style="float:left; width:40%;">

        <a href="https://emrepublishing.com/embellisher-ereader/?bookid='.$B['id'].'" target="_blank">
        	<img aria-hidden="true" style="width:90%;" src="'.$B['coverHref'].'" alt="cover" /> 
        </a>
    </div>
    <div style="float:left; width:60%;">

        
        <p style="font-size:10px;color:#222;">'.htmlentities($B['description']).'</p>
    </div>
     
</div>';
	if ($even){
		$WeeklyMail .= '<hr>';
	}

	if ($even){
		$even = false;
	}else{
		$even = true;
	}
}


$WeeklyMail .= '
<table class="DividerMargin" style="margin-bottom:5px;margin-top:5px;background-color: rgb(0, 215, 251);" bgcolor="#00D7FB" border="0" width="100%" cellpadding="15" id="content_LETTER.BLOCK6"><tr><td style="color: #000000;" width="100%" rowspan="1" colspan="1" align="left">
<div class="AboutUs" style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:18pt;">About Us</div>
<div class="AboutUsText" style="color:#000000;font-family:Arial,Helvetica,sans-serif;font-size:10pt;text-align: justify;" align="justify">EMRE Publishing, LLC has the only multimedia eReader, Forum and Creator Studio inside one mobile application. &nbsp;Once you learn how to use it, you\'ll be on the cutting edge of creation and marketing to the fastest growing readership in the world. &nbsp;</div>
<div class="AboutUsText" style="color:#000000;font-family:Arial,Helvetica,sans-serif;font-size:10pt;"><br /></div>
<div class="AboutUsText" style="color:#000000;font-family:Arial,Helvetica,sans-serif;font-size:10pt;">Welcome to our team of innovative creators and marketers.</div>
<div>
<table class="imgCaptionTable OneColumnMobile" style="text-align: center;" width="100" data-padding-converted="true" cellpadding="0" cellspacing="0" align="none"><tr><td class=" imgCaptionImg" style="padding-top: 5px; padding-right: 0px; padding-left: 0px; color: #000000;" width="1%" rowspan="1" colspan="1">
<div align="center"><img class="cc-image-resize" height="100" vspace="0" name="ACCOUNT.IMAGE.9" border="0" hspace="0" width="100" src="https://mlsvc01-prod.s3.amazonaws.com/8001c1cd401/ea3e19e1-0fec-436b-8fb3-6d5b8d01f93f.jpg" /></div>
</td></tr><tr><td class="imgCaptionText" style="padding-bottom: 5px; text-align: center; font-style: normal; font-weight: normal; color: #000000; font-family: Times New Roman;" rowspan="1" colspan="1"><a style="color: rgb(0, 0, 0); text-decoration: underline;" href="mailto:publisher@emrepublishing.com" shape="rect" linktype="2" target="_blank">Jim Musgrave</a><br />CEO, EMRE Publishing, LLC</td></tr></table>
</div>
</td></tr></table>
                      
                      </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td height="10" valign="top" width="100%" rowspan="1" colspan="3" align="left" />
          </tr>
        </table></td>
    </tr>
  </table>
</div>

</body>
</html>';

echo $WeeklyMail;
exit();

$GET_USERS = "SELECT * FROM user WHERE subscribed>0";
$USERS = $DB->query($GET_USERS);
while ($user = $USERS->fetch_assoc()){
	$username = $user['email'];
	$name = $user['name'];
	$to = $username;

	$subject = "Update: Check these new Ebooks!";
	$email_from = "noreply@emrepublishing.com";
	$htmlemail = $WeeklyMail;
	$htmlemail = str_replace("##EMAIL_PLACEHOLDER##", $username, $htmlemail);
	$htmlemail = str_replace("##NAME_PLACEHOLDER##", $name, $htmlemail);
	$headers = 'From: '.$email_from."\r\n".'Reply-To: '.$email_from."\r\n" .'X-Mailer: PHP/' . phpversion()."\r\nContent-type: text/html\r\n";
	@mail($to, $subject, $htmlemail, $headers);
}
