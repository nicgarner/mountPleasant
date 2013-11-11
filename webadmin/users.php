<?php
	
	$id = $_GET['id'];
	
	include '../includes/connect.php'; 
	include '../includes/functions.php'; 
	include '../includes/FCKeditor/fckeditor.php';
	include '../includes/emailtemplates.php';

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
            <meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, Jesus, Holy, Spirit, Kettering Road, Paul, Lavender, service, services, Mount, Pleasant, town, centre" />
            <meta name="Description" content="Mount Pleasant Baptist Church, 147 Kettering Road, Northampton" />
            <link rel="shortcut icon" href="../admin/images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="../admin/images/favicon.ico" type="image/ico" />
            <link rel="stylesheet" media="all" type="text/css" href="../site.css" /> 
            
            <script type="text/javascript">
				function getUpdatedText(){
					var objFckEditor = FCKeditorAPI.GetInstance('email');
					var updatedText = objFckEditor.EditorDocument.body.innerHTML;
					alert(updatedText );
				}
				function loadtemplate(optionvalue) {
					switch(optionvalue) {
						case "0" :
							document.getElementById("subject").value="";
							FCKeditorAPI.GetInstance("email").Commands.GetCommand("NewPage").Execute();
							break;

						case "1" :
							document.getElementById("subject").value="<? echo $emailSubjectAll ?>";
							FCKeditorAPI.GetInstance("email").Commands.GetCommand("NewPage").Execute();
							FCKeditorAPI.GetInstance("email").SetData("<? echo addslashes($emailTemplateAll) ?>");
							break;
				
						case "2" :
							document.getElementById("subject").value="<? echo $emailSubjectLinkup ?>";
							FCKeditorAPI.GetInstance("email").Commands.GetCommand("NewPage").Execute();
							FCKeditorAPI.GetInstance("email").SetData("<? echo addslashes($emailTemplateLinkup) ?>");
							break;
				
						case "3" :
							document.getElementById("subject").value="<? echo $emailSubjectMessages ?>";
							FCKeditorAPI.GetInstance("email").Commands.GetCommand("NewPage").Execute();
							FCKeditorAPI.GetInstance("email").SetData("<? echo addslashes($emailTemplateMessages) ?>");
							break;
						
						case "4" :
							document.getElementById("subject").value="<? echo $emailSubjectMessages1 ?>";
							FCKeditorAPI.GetInstance("email").Commands.GetCommand("NewPage").Execute();
							FCKeditorAPI.GetInstance("email").SetData("<? echo addslashes($emailTemplateMessages1) ?>");
							break;
					}
				}
			</script>

		</head>

		<body>
        
        <div class="site">
            
            <a href="/"><img src="../images/layout/banner.jpg" border="0" /></a>
            
            <div class="menu"><a href="../index.php">Home</a></div>
            
            
                
                <div class="content">
                
<?php
				
	if (isset($_COOKIE["cookie:mpbcadmin"])) {
		
		$id = $_GET['id'];
		if ($id == NULL) {
			$id = $_POST['id'];
		}
		$action = $_GET['action'];
		$type = $_GET['type'];
		
		if ($id <> NULL) {
		
			echo '<h3>Edit user</h3>';
			echo '<p align="right"><a href="' . $_SERVER['PHP_SELF'] . '">< back to Users</a></p>';
			
			if ($action == 'deny') {
				$query = "UPDATE users SET confirmed='2', role='0' WHERE user_id='$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				echo '<div class="confirm">This account has been deactivated.</div>';
				$confirmed = 2;
			}
			
				if ($_POST['submit']) {
					$firstname  = $_POST['firstname'];
					$lastname   = $_POST['lastname'];
					$email	    = $_POST['email'];
					$uusername  = $_POST['hiddenusername'];
					$password   = $_POST['password'];
					$confirm    = $_POST['confirm'];
					$role 	    = $_POST['role'];
					$confirmed  = $_POST['confirmed'];
					if ($confirmed <> 0) {
						$deactivate = $_POST['deactivate'];
						if ($deactivate == 'on') {
							if ($confirmed <> 2) {
								$confirmed = 2;
								$deactivated = 'deactivated';
							}
						}
						else {
							if ($confirmed <> 1) {
								$confirmed = 1;
								$deactivated = 'activated';
							}
						}
					}
	
					if ($firstname == NULL) {
						$error1 = 1;
						$error  = 1;
					}
					if ($lastname == NULL) {
						$error2 = 1;
						$error  = 1;
					}
					if ($email == NULL) {
						$error3 = 1;
						$error  = 1;
					}
					if ($password == NULL) {
						$error5  = 1;
						$error6  = 1;
						$error   = 1;
						$confirm = NULL;
					}
					if ($confirm == NULL) {
						$error5   = 1;
						$error6   = 1;
						$error    = 1;
						$password = NULL;
					}
					if ($password <> NULL && $confirm <> NULL) {
						if ($password <> $confirm) {
							$error5   = 1;
							$error6   = 1;
							$password = NULL;
							$confirm  = NULL;
							$error7   = 1;
							$error    = 1;
						}
					}
					if ($role == 0)	{
						$error  = 1;
						$error8 = 1;
					}
					
					if ($error == 1) {
						echo '<div class="error"><p>There was a problem with the information you provided. ';
						echo 'Please check the sections highlighted below.</p></div><br />';
					}
					
					else {
						if ($confirmed == '0') {
							$query = "UPDATE users SET name='$firstname', surname='$lastname', email='$email', password='$password', role='$role', confirmed='1' WHERE user_id='$id'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
							
	$reply = $emailHead.'
			
	<p>Hi '.$firstname.',</p>
			
	<p>Thank you for registering on the Mount Pleasant Baptist Church website. Your account is now activated, so you can log into the site at any time with your username and password:</p>
	<ul>
	<li>Username: '.$uusername.'</li>
	<li>Password: As supplied when you registered.</li>
	</ul>
	
	<p>We suggest you keep this email to remind you of these details. If you forget your password you can reset it by going to the log in page on the website and clicking \'I\'ve forgotten my password.\'</p>
			
	<p>If you did not register on the site with this email address, please reply to this email with the word DEACTIVATE in the message and we will deactivate the account.</p>
	
	<p>We hope you enjoy using the site.</p>
	
	<p>The Mount Pleasant Website Team</p></body></html>';
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";
	
	mail($email, 'Mount Pleasant Baptist Church account registration', $reply, $headers);						
							
							echo '<div class="confirm">This account has been activated and an email has been sent to notify the user.</div>';
							$confirmed = 1;
						}
						else {
							$query = "UPDATE users SET name='$firstname', surname='$lastname', email='$email', password='$password', role='$role', confirmed='$confirmed' WHERE user_id='$id'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
							if ($deactivated == 'deactivated') {
								echo '<div class="confirm"><p>This account has been deactivated.</p></div>';
							}
							elseif ($deactivated == 'activated') {
								echo '<div class="confirm"><p>This account has been reactivated.</p></div>';
							}
							else {
								echo '<meta http-equiv="refresh" content="0;url=users.php">';
							}
						}
					}
				}
			
		
			else {
				$query  = "SELECT user_id, name, surname, username, password, email, role, confirmed FROM users WHERE user_id = '$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$id        = $row['user_id'];
					$firstname = $row['name'];
					$lastname  = $row['surname'];
					$email	   = $row['email'];
					$uusername = $row['username'];
					$password  = $row['password'];
					$confirmed = $row['confirmed'];
					$role	   = $row['role'];
				}
			}
			
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" autocomplete="off">';
				if ($confirmed == 0) {
					echo '<div class="error"><p>This account hasn\'t been activated yet. Please check the details below and select a role for this user, then click Activate Account.</p></div><br />';
					echo '<p align="center"><input type="submit" name="submit" value="Activate Account"> ';
					echo '<a class="button" href="' . $_SERVER['PHP_SELF'] . '?id='.$id.'&action=deny">Deny Account</a></p>';
				}
				echo '<input name="confirmed" size=1 value="'.$confirmed.'" type="hidden">';
				echo '<h4>Personal details</h4>';
				echo '<input name="id" size=10 value="'.$id.'" type="hidden">';
				echo '<div class="label">First name:</div> <input name="firstname" size=30 value="'.$firstname.'"';
				if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Last name:</div> <input name="lastname" size=30 value="'.$lastname.'"';
				if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Email address:</div> <input name="email" size=30 value="'.$email.'"';
				if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<h4>Account details</h4>';
				echo '<div class="label">Username:</div> <input name="username" size=30 value="'.$uusername.'" disabled="disabled"><br class="clear" />';
				echo '<input name="hiddenusername" size=30 value="'.$uusername.'" type="hidden">';
				if ($error7 == 1) { echo '<div class="error"><p>The passwords you gave do not match. Please try again.</p></div>'; }
				echo '<div class="label">Password:</div> <input type="password" name="password" size=30" value="'.$password.'"';
				if ($error5 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Confirm password:</div> <input type="password" name="confirm" size=30" value="'.$password.'"';
				if ($error6 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				if ($error8 == 1) { echo '<div class="error"><p>Please select the appropriate role for this account.</p></div>'; }
				$query = "SELECT role_id, role FROM roles ORDER BY role ASC";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						
				// check if records were returned
				if (mysql_num_rows($result) > 0) {
					echo '<div class="label">Role:</div> <select name="role"';
					if ($error8 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '>';
					if ($confirmed == 0 || $role == 0) {
						echo '<option value="0">Select role</option>';
					}
					while($row = mysql_fetch_object($result)) {
						echo '<option value="' . $row->role_id . '"';
							if ($role == $row->role_id) {
								echo ' SELECTED';
							}
						echo '>' . $row->role . '</option>';
					}
					echo '</select><br class="clear" />';
				}
				else {
					echo 'There are no categories in the database!<br>';
				}
				if ($confirmed <> 0) {
					echo '<div class="label">Deactive account:</div> <input type="checkbox" name="deactivate"';
					if ($confirmed == '2') {
						echo ' checked="checked"';
					}
					echo '><br class="clear" /><br />';
					echo '<div class="label"></div><p class="submit"><input type="submit" name="submit" value="save changes"></p>';
				}
			echo '</form>';
		}		
		
		else {
			if ($action == 'email') {
				echo '<h3>Email users</h3>';
				echo '<p align="right"><a href="' . $_SERVER['PHP_SELF'] . '">< back to Users</a></p>';
				
				if ($_POST['submit']) {
					$to  	 = $_POST['to'];
					$subject = $_POST['subject'];
					$email   = $_POST['email'];
					
					if ($to == 0) {
						$error1 = 1;
						$error  = 1;
					}
					if ($subject == NULL) {
						$error2 = 1;
						$error  = 1;
					}
					if ($email == NULL) {
						$error3 = 1;
						$error  = 1;
					}
					
					if ($error == 1) {
						echo '<div class="error"><p>Please fill in all fields.</p></div><br />';
					}
					
					else {

						if ($to == 1)
							$query  = "SELECT name, surname, email FROM users WHERE confirmed = 1 OR confirmed = 3";
						
						elseif ($to == 2)
							$query  = "SELECT name, surname, email FROM users WHERE emailLinkup = 1 AND (confirmed = 1 OR confirmed = 3)";
						
						elseif ($to == 3)
							$query  = "SELECT name, surname, email FROM users WHERE emailMessage = 1 AND (confirmed = 1 OR confirmed = 3)";
						elseif ($to == 4)
							$query  = "SELECT name, surname, email FROM users WHERE emailMessage = 1 AND (confirmed = 1 OR confirmed = 3)";
							
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						if (mysql_num_rows($result) > 0) {
							
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";
							
							$email = stripslashes($email);
							preg_match_all("@[\[]{2}[a-zA-Z_: |.\-',0-9]+[\]]{2}@", $email, $matches);
							
							$count == 0;
							
							while($row = mysql_fetch_object($result)) {
								$replace = array();
								foreach ($matches as $variable) {
									foreach ($variable as $avariable) {
										$link = str_replace(array("[", "]"), "", trim($avariable));
										if ($link == 'firstname') {
											array_push($replace, $row->name);
										}
										elseif ($link == 'lastname') {
											array_push($replace, $row->lastname);
										}
									}
								}
								$parsed_email = str_replace($matches[0][0], $replace[0], $email);
//								echo $parsed_email.'<br><hr><br>';
								$parsed_email = $emailHead.$parsed_email.'</body></html>';												
								mail($row->email, $subject, $parsed_email, $headers);
								$count++;
							}
							echo '<div class="confirm"><p>'.$count.' emails sent.</p></div><br class="clear" />';
						}
						else {
							echo '<div class="error"><p>No emails were sent.</p></div><br class="clear" />';
						}
						$to = 0;
						$subject = "";
						$email = "";
						
					}
				}
				echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=email">';
					echo '<div class="label">Send to:</div>';
					echo '<select id="loadTemplate" name="to" onchange=\'loadtemplate(this.value);\'';
						if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; }
						echo '>';
						echo '<option value="0">Select addressees</option>';
						echo '<option value="1"';
							if ($to == 1) { echo ' SELECTED'; } echo '>All members</option>';
						echo '<option value="2"';
							if ($to == 2) { echo ' SELECTED'; } echo '>Link-Up subscribers</option>';
						echo '<option value="3"';
							if ($to == 3) { echo ' SELECTED'; } echo '>Sermon subscribers (two sermons)</option>';
						echo '<option value="4"';
							if ($to == 4) { echo ' SELECTED'; } echo '>Sermon subscribers (one sermon)</option>';
					echo '</select><br class="clear" />';
					echo '<div class="label">Email subject:</div> <input id="subject" name="subject" size="60" value="'.$subject.'"';
					if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
					echo '<div class="label">Variables:</div><p>[[firstname]] / [[lastname]]</p>';
								
					$oFCKeditor = new FCKeditor('email') ; 
					$oFCKeditor->BasePath	= '../includes/FCKeditor/';
					$oFCKeditor->ToolbarSet = 'Email';
					$oFCKeditor->Value = stripslashes($email);
					$oFCKeditor->Height = 400;
					$oFCKeditor->Create();
					
				echo '<br />';
				echo '<p><input type="submit" name="submit" value="Send email"></p>';
				echo '</form>';
			}
			else {
				echo '<h3>Manage users</h3>';
				echo '<h4>Email users</h4>';
				echo '<p><a href="'.$PHP_SELF.'?action=email">Click here to email users or selected users.</a></p>';
				
				$query = "SELECT COUNT(*) FROM users WHERE confirmed = 0";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$row = mysql_fetch_row($result);
				$new_users = $row[0];
				
				if ($new_users > 0) {
					$query_new = "SELECT user_id, name, surname, username, email FROM users WHERE confirmed = 0 ORDER BY surname ASC";
				}
				
				$query = "SELECT COUNT(*) FROM users WHERE confirmed = 1 OR confirmed = 3";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$row = mysql_fetch_row($result);
				$users = $row[0];
				
				$start = $_GET['start'];
				if ($start == NULL) {
					$start = 0;
				}
											
				echo '<h4>New Users</h4>';
				echo '<p>There are <b>'.$new_users.'</b> new users waiting to be confirmed';
				if ($new_users > 0) {
					echo ':</p>';
					echo '<ul>';
					$result = mysql_query($query_new) or die ("Error in query: $query_new. " . mysql_error());
					while ($row = mysql_fetch_assoc($result)) {
						echo '<li><a href="'.$PHP_SELF.'?'.$page.'&id='.$row['user_id'].'">'.$row['name'].' '.$row['surname'].'</a></li>';
					}
					echo '</ul>';
				}
				else {
					echo '.</p>';
				}
				
				echo '<h4>Users</h4>';
				echo '<p>There are <b>'.$users.'</b> registered users.';
				
				$rpp = '20';
				
	#			if (($users > 0) && ($start < $users)) {
					$query  = "SELECT user_id, name, surname, username, email, role FROM users WHERE confirmed = 1 ORDER BY surname ASC";
	#				$query .= " LIMIT $start, $rpp";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					echo '<ul>';
					while ($row = mysql_fetch_assoc($result)) {
						echo '<li><a href="'.$PHP_SELF.'?'.$page.'&id='.$row['user_id'].'">'.$row['name'].' '.$row['surname'].'</a></li>';
					}
					echo '</ul>';
	#			}
	#			else {
	#				echo '<p>There are no users.</p>';
	#			}
												
	#			echo '<tr><td colspan="4" bgcolor="#00584c" align="center"><font color="#FFFFFF">' . $msg_unread . ' unread ';
	#				if ($msg_unread == 1) {
	#					echo 'message, ';
	#				}
	#				else {
	#					echo 'messages, ';
	#				}
	#			echo $messages . ' total.</font></td></tr>';
	#			echo '<tr><td colspan="2" align="left">';
	#			if ($start >= $rpp) {
	#				echo '<a href=' . $_SERVER['PHP_SELF'] . '?start=' . ($start-$rpp) . '>Previous ' . $rpp . ' messages</a>';
	#			}
	#			echo '</td><td colspan="2" align="right">';
	#			if ($start+$rpp < $messages) {
	#				echo '<a href=' . $_SERVER['PHP_SELF'] . '?start=' . ($start+$rpp) . '>Next ' . $rpp . ' messages</a>';
	#			}
	#			echo '</td></tr></table>';
				
				$query = "SELECT COUNT(*) FROM users WHERE confirmed = 2";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$row = mysql_fetch_row($result);
				$old_users = $row[0];
				
				if ($old_users > 0) {
					$query_old = "SELECT user_id, name, surname, username, email FROM users WHERE confirmed = 2 ORDER BY surname ASC";
				
											
					echo '<h4>Deactivated Users</h4>';
					echo '<p>There are <b>'.$old_users.'</b> accounts that have been deactivated:';
						echo '<ul>';
						$result = mysql_query($query_old) or die ("Error in query: $query_old. " . mysql_error());
						while ($row = mysql_fetch_assoc($result)) {
							echo '<li><a href="'.$PHP_SELF.'?'.$page.'&id='.$row['user_id'].'">'.$row['name'].' '.$row['surname'].'</a></li>';
						}
						echo '</ul>';
				}
			}
		
		}
		
	?>

</div>

<div class="whiteside">
    <a href="../index.php?page=login"><h4>Admin Home</h4></a>
    
   <? printadminmenu(); ?>

<?php
}
?>
</div>
<?php include('../includes/footer.php'); ?>
</div>
</body> 
</html>
