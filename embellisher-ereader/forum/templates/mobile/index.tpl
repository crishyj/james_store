<!DOCTYPE html>
<html>
<head>
<title>{HTML_TITLE}</title>
{IF URL->REDIRECT}
  <meta http-equiv="refresh" content="{IF REDIRECT_TIME}{REDIRECT_TIME}{ELSE}5{/IF}; url={URL->REDIRECT}" />
{/IF}
<style>
/*
Copyright (c) 2009, Nicole Sullivan. All rights reserved.
Code licensed under the BSD License:
*/
.main{display:table-cell;width:auto;}
.body:after,.main:after{clear:both;display:block;visibility:hidden;overflow:hidden;height:0 !important;line-height:0;font-size:xx-large;content:" x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x ";}
.page{margin:0 auto;width:950px;}
/* ====== Columns ====== */
.leftCol{float:left;width:250px;}
.rightCol{float:right;width:300px;}
.line:after,.lastUnit:after{clear:both;display:block;visibility:hidden;overflow:hidden;height:0 !important;line-height:0;font-size:xx-large;content:" x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x ";}
.unit{float:left;}
.size1of1{float:none;}
.size1of2{width:50%;}
.size1of3{width:33.33333%;}
.size2of3{width:66.66666%;}
.size1of4{width:25%;}
.size3of4{width:75%;}
.size1of5{width:20%;}
.size2of5{width:40%;}
.size3of5{width:60%;}
.size4of5{width:80%;}
.lastUnit{display:table-cell;float:none;width:auto;}

@media screen and (max-width: 319px) {.unit{float: none !important; width: auto !important;}}

<?php stripos($_SERVER["HTTP_USER_AGENT"], "mobile")===false) { ?>
body {background: Gray}
#w{width: 320px; margin: auto;}
<?php }?>

</style>
</head>
<body onload="{IF FOCUS_TO_ID}var focuselt=document.getElementById('{FOCUS_TO_ID}'); if (focuselt) focuselt.focus();{/IF}">
<div id="w">

{! This <div> holds info about the active page (heading and description) }
<div id="page-info">
{IF HEADING}
{! This is custom set heading }
<h1 class="heading">{HEADING}</h1>
{ELSEIF MESSAGE->subject}
{! This is a threaded read page }
<h1 class="heading">{MESSAGE->subject}</h1>
{ELSEIF TOPIC->subject}
{! This is a read page }
<h1 class="heading">{TOPIC->subject}</h1>
{ELSEIF NAME}
{! This is a forum page other than a read page or a folder page }
<h1 class="heading">{NAME}</h1>
{ELSE}
{! This is the index }
<h1 class="heading">{TITLE}</h1>
{/IF}
</div> <!-- end of div id=page-info -->


</div>
</body>
</html>
