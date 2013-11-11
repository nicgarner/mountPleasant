<?php

$emailHead = '<html><head><style type="text/css">p{font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size:12px;margin:0 0 10px 0;line-height:150%;}li{font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;font-size:12px;margin:0 0 10px 0;line-height:150%;}</style></head><body>';

$emailSubjectAll  = 'Mount Pleasant Baptist Church website';
$emailTemplateAll = '<p>Dear [[firstname]],</p><p>&nbsp;</p><p>Thanks</p><p>The Mount Pleasant Website Team</p>';

$query  = "SELECT DATE_FORMAT(date, '%M') as month FROM linkup WHERE deleted='0' ";
$query .= "ORDER BY date DESC LIMIT 0, 1";  
$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
while($row = mysql_fetch_object($result)) {
	$latestLinkup = $row->month;
}

$emailSubjectLinkup  = 'New edition of Link-Up available';
$emailTemplateLinkup = '<p>Dear [[firstname]],</p><p>The '.$latestLinkup.' edition of the Mount Pleasant Link-Up magazine is now available on the website. You can find it on the <a href="http://www.mountpleasantchurch.com/link_up">Link-Up</a> page. Log in to the website in order to access the private articles.</p><p>Thanks</p><p>The Mount Pleasant Website Team</p><p><font size="1">You are receiving this email as a member of the Mount Pleasant Baptist Church website. To stop receiving these emails, <a href="https://www.mountpleasantchurch.com/login">log in</a> to your account and change your preferences.</font></p>';

$query  = "SELECT name, speaker, DATE_FORMAT(date, '%W %D %M %Y') as datef, ";
$query .= "TIME_FORMAT(date, '%l.%i %p') as time FROM recordings ";
$query .= "WHERE deleted = 0 ";
$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i') ASC LIMIT 0, 2";
$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
$i = 1;
while($row = mysql_fetch_object($result)) {
	if ($i == 1)
		$message1='<b>'.$row->name.'</b><br />'.$row->speaker.', '.$row->datef.' '.strtolower($row->time);
	elseif ($i == 2)
		$message2='<b>'.$row->name.'</b><br />'.$row->speaker.', '.$row->datef.' '.strtolower($row->time);									
	$i++;
}

$emailSubjectMessages  = 'New Sunday messages are available';
$emailTemplateMessages = '<p>Dear [[firstname]],</p><p>New Sunday message recordings are now available on the Mount Pleasant website. You can listen to them on the <a href="http://www.mountpleasantchurch.com/sunday_messages">Sunday Messages</a> page.</p><p>The new messages are:</p><ul><li>'.$message1.'</li><li>'.$message2.'</li></ul><p>Thanks</p><p>The Mount Pleasant Website Team</p><p><font size="1">You are receiving this email as a member of the Mount Pleasant Baptist Church website. To stop receiving these emails, <a href="https://www.mountpleasantchurch.com/login">log in</a> to your account and change your preferences.</font></p>';

$emailSubjectMessages1  = 'A new Sunday message is available';
$emailTemplateMessages1 = '<p>Dear [[firstname]],</p><p>A new Sunday message recording is now available on the Mount Pleasant website. You can listen to it on the <a href="http://www.mountpleasantchurch.com/sunday_messages">Sunday Messages</a> page.</p><p>The new message is:</p><ul><li>'.$message1.'</li></ul><p>Thanks</p><p>The Mount Pleasant Website Team</p><p><font size="1">You are receiving this email as a member of the Mount Pleasant Baptist Church website. To stop receiving these emails, <a href="https://www.mountpleasantchurch.com/login">log in</a> to your account and change your preferences.</font></p>';

?>
