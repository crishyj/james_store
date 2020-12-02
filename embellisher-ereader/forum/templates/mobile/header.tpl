<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="HandheldFriendly" content="true">
<title>{HTML_TITLE}</title>
{IF URL->REDIRECT}
  <meta http-equiv="refresh" content="{IF REDIRECT_TIME}{REDIRECT_TIME}{ELSE}5{/IF}; url={URL->REDIRECT}" />
{/IF}
<style>
{!
    VERY IMPORTANT!!
    FOR MOBILE, IT IS BEST TO DELIVER THE CSS WITH THE HTML FOR SPEED
    HOWEVER, BECAUSE OF PHORUM'S TEMPLATE SYSTEM, IT LOOKS FOR THINGS IN CURLY
    BRACES AND TREATS IT LIKE A VARIABLE OR COMMAND. YOU MUST USE A SPACE AFTER
    THE { IN THE CSS HERE.
}
/*
OOCSS - https://github.com/stubbornella/oocss http://oocss.org/
Copyright (c) 2009, Nicole Sullivan. All rights reserved.
Code licensed under the BSD License
modified for use here
*/
.line:after,.lastUnit:after{ clear:both;display:block;visibility:hidden;overflow:hidden;height:0 !important;line-height:0;font-size:xx-large;content:" x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x x ";}
.unit{ float:left;}
.size1of1{ float:none;}
.size1of2{ width:50%;}
.size1of3{ width:33.33333%;}
.size2of3{ width:66.66666%;}
.size1of4{ width:25%;}
.size3of4{ width:75%;}
.size1of5{ width:20%;}
.size2of5{ width:40%;}
.size3of5{ width:60%;}
.size4of5{ width:80%;}
.lastUnit{ display:table-cell;float:none;width:auto;}
@media screen and (max-width: 319px) { .unit{ float: none !important; width: auto !important;}}
/* End OOCSS */
body{ padding:0;margin:0;font-family:Arial,Helvetica}
a{ color:#6173f6}
h1,h2,h3,h4,h5,h6{ margin:0;color:#666d5e}
h1{ font-size:150%;padding:3px;background-image:-webkit-gradient(linear,left bottom,left top,color-stop(0.28,#3235f6),color-stop(0.89,#3235f6));background-image:-moz-linear-gradient(center bottom,#5f61dd 28%,#3235f6 89%)}
h1,h1 a{ color:White}
h2{ font-size:140%;padding:3px;background-color:#e6e6e6;margin-bottom:5px;-webkit-box-shadow:0 3px 4px #bdbdbd;-moz-box-shadow:0 3px 4px #bdbdbd;box-shadow:0 3px 4px #bdbdbd}
h3{ font-size:130%}
h4{ font-size:120%}
h5{ font-size:110%}
h6{ font-size:100%}
h2 a:link,h2 a:visited{ color:#5f5e6d;text-decoration:none}
h3 a:link,h3 a:visited{ text-decoration:none}
h4 a:link,h4 a:visited{ text-decoration:none}
h5 a:link,h5 a:visited{ text-decoration:none}
h6 a:link,h6 a:visited{ text-decoration:none}
input[type=text],input[type=password],textarea{ font-size:130%;width:95%;padding:4px}
textarea{ height:200px}
#post-buttons{ text-align:center}
#folder-link{ display:block;position:absolute;top:0;right:8px;text-decoration:none;background:Gray;color:white;padding:4px;-webkit-border-bottom-right-radius:5px;-webkit-border-bottom-left-radius:5px;-moz-border-radius-bottomright:5px;-moz-border-radius-bottomleft:5px;border-bottom-right-radius:5px;border-bottom-left-radius:5px}
.list{ padding:3px;margin-top:3px;border-bottom:1px solid #e6e6e6}
.list .info{ text-align:right;color:#5f5e6d}
.paging{ color:#5f5e6d;padding:6px 4px;font-size:120%}
.paging .lastUnit{ text-align:right}
.information{ text-align:center}
.pad{ padding:8px}
.nav .unit a{ font-size:120%;color:white;display:block;text-decoration:none;text-align:center;margin-left:1px;padding:3px 0;background-image:-webkit-gradient(linear,left bottom,left top,color-stop(0.28,#5f61dd),color-stop(0.89,#3235f6));background-image:-moz-linear-gradient(center bottom,#5f61dd 28%,#3235f6 89%)}
.nav .unit:first-child a{ margin:0}
.message{ margin-bottom:8px}
.body{ padding:8px;overflow:hidden;font-size:120%;margin-bottom:27px}
.message-options{ text-align:right}
.preview{ border-bottom:1px solid #e6e6e6}
.check{ padding:0 8px 0 4px}
.header tr{ text-align:left;padding-right:8px}
.new{ font-weight:bold}
.newind{ margin:0 8px 0 4px;width:10px;height:10px;background-color:#215099;-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;display:inline-block}
<?php if(stripos($_SERVER["HTTP_USER_AGENT"], "mobile")===false) { ?>
body { background: Gray}
#w{ max-width: 320px; margin: auto; background: white; position: relative;}
<?php }?>
</style>
<script>
function doOnLoad() {
    setTimeout(function() { window.scrollTo(0, 1) }, 100);
    {IF FOCUS_TO_ID}var focuselt=document.getElementById('{FOCUS_TO_ID}'); if (focuselt) focuselt.focus();{/IF}
}
</script>
</head>
<body onload="doOnLoad();">
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
