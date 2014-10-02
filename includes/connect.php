<?php 
/* access your database information where ever it is */
// $db = mysql_connect('localhost', 'root', '');

// for live server
$db = mysql_connect('localhost', 'anonymised', 'anonymised');

/* if there is a problem connecting to the DB - tell me and display
any error messages given then stop the program gracefully */

if (!$db)
{
echo '



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Mount Pleasant Baptist Church, Northampton</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:bold;
	margin-left:auto;
	margin-right:auto;
}
	
.style2 {
	color: #008476;
	font-weight: bold;
}
-->
</style>
</head>

<body bgcolor="#CCCCCC">

<table align="0" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr><td width="100%" align="center" valign="middle"><p><img src="splash.jpg" border="0"></p><br />
<div class="style1">The website should be running as normal soon.</div></td></tr>
</tr></table>

</body>
</html>

';

die;
}
$link = mysql_select_db('mpbcdb');

/* if there is a problem selecting the DB - tell me and display
any error messages given then stop the program gracefully */
if (!$link)
{
echo '



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Mount Pleasant Baptist Church, Northampton</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:bold;
	margin-left:auto;
	margin-right:auto;
}
	
.style2 {
	color: #008476;
	font-weight: bold;
}
-->
</style>
</head>

<body bgcolor="#CCCCCC">

<table align="0" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr><td width="100%" align="center" valign="middle"><p><img src="splash.jpg" border="0"></p><br />
<div class="style1">The website should be running as normal soon.</div></td></tr>
</tr></table>

</body>
</html>

';
die;
} 

?>
