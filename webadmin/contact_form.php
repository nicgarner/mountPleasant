<?php
	
	$id = $_GET['id'];
	
	include '../includes/connect.php'; 
	include '../includes/functions.php'; 
	
	if (isset($_COOKIE["cookie:mpbcadmin"])) {
		$uid = $_COOKIE['cookie:mpbcadmin'];
		$query = "SELECT name FROM users WHERE user_id = $uid";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		while ($row = mysql_fetch_assoc($result)) {
			$username = $row['name'];
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Mount Pleasant Baptist Church, Northampton</title>
            <meta name="Author" content="Mount Pleasant Baptist Church" />
            <meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, Jesus, Holy, Spirit, Kettering Road, Jeff, Taylor, Paul, Lavender, service, services, Mount, Plesant, town, centre" />
            <meta name="Description" content="Mount Plesant Baptist Church, 147 Kettering Road, Northampton" />
            <link rel="shortcut icon" href="../admin/images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="../admin/images/favicon.ico" type="image/ico" />
            <link rel="stylesheet" media="all" type="text/css" href="../site.css" />

		</head>

		<body>
        
        <div class="site">
            
            <a href="/"><img src="../images/layout/banner.jpg" border="0" /></a>
            
            <div class="menu"><a href="../index.php">Home</a></div>
            
            
                
                <div class="content">
                
                <?php
				
                if (isset($_COOKIE["cookie:mpbcadmin"])) {
				
				?>
                
					<h3>Contact Form</h3>
					
    <?php
		
		$id = $_GET['id'];
		$a = $_GET['a'];
		$c = $_GET['c'];
		
		if ($_POST['submit']) {
			$from_email = $_POST['from_email'];
			$reply = $_POST['reply'];
			$date = date("Y-m-d G:i:s");
			
			if ($message == NULL && $from_email == NULL) {
				echo '<div class="error">You didn\'t enter a message or your email address.</div>';
			}			
			elseif ($message == NULL) {
				$error = '<div class="error">You didn\'t enter a message.</div>';
			}
			elseif ($from_email == NULL) {
				$error = '<div class="error">You didn\'t enter your email address.</div>';
			}
			else {
				
				echo '<div="confirm">Thank you, your reply has been sent.</div><br><br>';
											
				$query = "INSERT INTO contact_form_replies(contact_form_id, reply, time, user_id) VALUES('$id', '$reply', '$date', '$uid')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				
				$query = "SELECT replies, email FROM contact_form WHERE contact_form_id = $id";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$replies = $row['replies'];
					$to_email = $row['email'];
				}
				
				$replies = $replies + 1;
				
				$query = "UPDATE contact_form SET replies='$replies' WHERE contact_form_id='$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				
				$reply = stripslashes($reply) . 
"

--------------

This message has been sent to you in response to a message submitted to www.mountpleasantchurch.com.  If you think this message has been received in error, then please reply to us to let us know, then delete the message and ignore it.";
				mail($to_email, 'Reply from Mount Pleasant Baptist Church', $reply, 'From: ' . $from_email);
				
				$a = NULL;
			}
		}
		
		if ($a == 'v') {
			echo '<div align="left">';
			echo '<p><a href="inbox.php?mid=' . $id . '">< Back to message</a></p>';
			$query = "SELECT replies.message_text, DATE_FORMAT(replies.time, '%d %M') AS date, users.name FROM replies, users WHERE replies.message_id = $id AND replies.user_id = users.user_id ORDER BY replies.time DESC";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			while ($row = mysql_fetch_assoc($result)) {
				echo '<table border="0" width="500" cellpadding="5"><tr valign="top"><td bgcolor="#6384A9" width="100">' . $row['date'] . '<br><br>From: ' . $row['name'] . '</td><td>' . nl2br($row['message_text']) . '</td></tr></table>';
				echo '<br><br>';
			}
			echo '<p align="center"><a href="inbox.php?mid=' . $mid . '&a=r"><font color="white">Click here to send another reply to this message.</a></p>';
		}
		
		if ($id <> NULL) {
		
			if ($a <> 'v') {
			
				$query = "UPDATE contact_form SET red='1' WHERE contact_form_id='$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$query = "SELECT contact_form_id, name, email, home, mobile, message, DATE_FORMAT(time, '%d %M, %H:%i') AS date FROM contact_form WHERE contact_form_id = $id";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					echo '<h4>Viewing message:</h4><br />';
					echo '<table border="0" cellpadding="5" cellspacing="0" width="500">';
					echo 	'<tr bgcolor="#9bd09a"><td>';
					echo 		'<table border="0" cellpadding="0" cellspacing="0">';
					echo 			'<tr><td width="60">From:</td><td width="230">';
									if ($row['name'] == NULL) {
										echo '(anonymous)';
									}
									else {
										echo $row['name'];
									}
					echo 			'</td>';
					echo 			'<td width="40">Date:</td><td width="150">' . $row['date'] . '</td></tr>';
					echo	'<tr height="6"><td colspan="4"></td></tr>';
					
					if ($row['email'] <> NULL) {
						echo	'<tr><td>Email:</td><td>'.$row['email'].'</td></tr>';
						echo	'<tr height="3"><td colspan="4"></td></tr>';
					}
					if ($row['home'] <> NULL) {
						echo	'<tr><td>Home:</td><td>'.$row['home'].'</td></tr>';
						echo	'<tr height="3"><td colspan="4"></td></tr>';
					}
					if ($row['mobile'] <> NULL) {
						echo	'<tr><td>Mobile:</td><td>'.$row['mobile'].'</td></tr>';
					}
					echo 		'</table>';
					echo	'</td></tr>';
					echo 	'<tr height="150"><td valign="top"><p>' . nl2br($row['message']) . '</p></td></tr>';
					echo	'<tr bgcolor="#9bd09a"><td align="center"><a href="'.$_SERVER['PHP_SELF'].'">Back to inbox</a>';
					if ($row['email'] <> NULL) {
						if ($row['replies'] > 0) {
							echo ' | <a href="'.$_SERVER['PHP_SELF'].'?id=' . $id . '&a=v">View ' . $row['replies'];
							if ($row['replies'] == 1) {
								echo ' reply';
							}
							else {
								echo ' replies';
							}
							echo ' to this message</a></td></tr>';
						}
						else {
							echo ' | <a href="'.$_SERVER['PHP_SELF'].'?id=' . $id . '&a=r">Reply to this message via email</a>';
						}
					}
					echo '</table>';
				}
				if ($a == 'r') {
					?>
						<form name="reply" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
							<input type="hidden" name="id" value="<?php echo $id; ?>">
							<h4>Send a reply:</h4><br />
                            <div class="label">From:</div>
                            <input name="from_email" size="40" maxlength="60" value="your email address" />
                            <textarea name="reply" rows="8" cols="60"></textarea><br /><br />
							<input type="submit" name="submit" value=" Send " class="submit">
						</form>
					<?php
				}
				if ($a == 'd') {
					if ($c == 't') {
						$query = "DELETE FROM messages WHERE message_id=$id";
						$result = mysql_query($query);
						echo '<p>Message deleted from inbox. <a href="inbox.php">Return to inbox.</a></p>';
						echo '<meta http-equiv="refresh" content="5;url=inbox.php">';
					}
					else {
						$query = "SELECT message_id, name, DATE_FORMAT(time, '%d %M') AS date FROM messages WHERE message_id=$id";
						$result = mysql_query($query);
						while ($row = mysql_fetch_assoc($result)) {
							echo '<p>Are you sure you want to delete this message?</p><p>From: <b>';
							if ($row['name'] == NULL) {
								echo '(anonymous)';
							}
							else {
								echo $row['name'];
							}
							echo '</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sent: <b>' . $row['date'] . '</b></p>';
							echo '<p><a href="inbox.php?id=' . $row['message_id'] . '&a=d&c=t">Delete</a> | <a href="inbox.php">Cancel</a>';
						}
					}
				}
			}
		}
		else {
		
			$query = "SELECT COUNT(*) FROM contact_form WHERE red = 0";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			$row = mysql_fetch_row($result);
			$msg_unread = $row[0];
			
			$query = "SELECT COUNT(*) FROM contact_form WHERE red = 1";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			$row = mysql_fetch_row($result);
			$msg_read = $row[0];
			
			$messages = $msg_unread + $msg_read;
			
			$start = $_GET['start'];
			if ($start == NULL) {
				$start = 0;
			} 
										
			echo '<p>This page displays messages sent via the Contact Form. The most recent messages are displayed at the top. Unread messages are shown with a green background. Click a name to view the entire message.</p>';
			
			echo '<table border="0" cellpadding="3" cellspacing="0">';
			echo '<tr bgcolor="#00584c"><td width="20"></td><td width="150"><b><font color="#FFFFFF">From</font></b><td width="250"><b><font color="#FFFFFF">Message</font></b></td><td width="100"><b><font color="#FFFFFF">Date</font></b></td></tr>';
			
			$rpp = '10';
			
			if (($messages > 0) && ($start < $messages)) {
				$query = "SELECT contact_form_id, name, email, home, mobile, message, DATE_FORMAT(time, '%d %M') AS date, red FROM contact_form ORDER BY time DESC LIMIT $start, $rpp";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					
					if ($row['red'] == 0) {
						echo '<tr bgcolor="#9bd09a">';
					}
					else {
						echo '<tr>';
					}
					echo '<td>';
/*					if ($row['replies'] > 0) {
						if ($row['replies'] == 1) {
							echo '<img border="0" alt="' . $row['replies'] . ' reply" src="images/replied.gif"><font size="1"> 1</font>';
						}
						else {
							echo '<img border="0" alt="' . $row['replies'] . ' replies" src="images/replied.gif"><font size="1"> ' . $row['replies'] . '</font>';
						}
					}
					elseif ($row['red'] == 0) {
						echo '<img border="0" alt="unread" src="images/unread.gif">';
					}
					else {
						echo '<img border="0" alt="read" src="images/read.gif">';
					}
*/
					echo '</td><td>';
					if ($row['name'] == NULL) {
						echo '<a href="'. $_SERVER['PHP_SELF'] .'?id=' . $row['contact_form_id'] . '">(anonymous)</a></font>';
					}
					else {
						echo '<a href="'. $_SERVER['PHP_SELF'] .'?id=' . $row['contact_form_id'] . '">';
						if (strlen($row['name']) < 24) {
							echo $row['name'];
						}
						else {
							echo substr(strip_tags($row['name']), 0, 22) . '...';
						} 
						echo '</a>';
					}
					echo '</td><td>';
					if (strlen($row['message']) < 34) {
							echo $row['message'];
					}
					else {
						echo substr(strip_tags($row['message']), 0, 32) . '...';
					} 
					echo '</td><td>' . $row['date']. '</td></tr>';
					echo '<tr height="1"><td colspan="4" bgcolor="#00584c"></td></tr>';
				}
			}
			else {
				echo '<tr><td colspan="4"><br>There are no messages to display.<br><br></td></tr>';
			}
											
			echo '<tr><td colspan="4" bgcolor="#00584c" align="center"><font color="#FFFFFF">' . $msg_unread . ' unread ';
				if ($msg_unread == 1) {
					echo 'message, ';
				}
				else {
					echo 'messages, ';
				}
			echo $messages . ' total.</font></td></tr>';
			echo '<tr><td colspan="2" align="left">';
			if ($start >= $rpp) {
				echo '<a href=' . $_SERVER['PHP_SELF'] . '?start=' . ($start-$rpp) . '>Previous ' . $rpp . ' messages</a>';
			}
			echo '</td><td colspan="2" align="right">';
			if ($start+$rpp < $messages) {
				echo '<a href=' . $_SERVER['PHP_SELF'] . '?start=' . ($start+$rpp) . '>Next ' . $rpp . ' messages</a>';
			}
			echo '</td></tr></table>';
			
		}
		
	?>

</div>

<div class="whiteside">
    <a href="../index.php?page=login"><h4>Admin Home</h4></a>
    
    <? printadminmenu(); ?>
    
</div>

<?php
}
?>

<?php include('../includes/footer.php'); ?>
</div>
</body> 
</html>