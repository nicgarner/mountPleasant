<?php

global $page;
global $user_id;
global $role;
global $ip;

include 'includes/emailtemplates.php';
include 'includes/recaptchalib.php';

# set email address of person to administer accounts here:
$user_admin = 'mpadmin@jaggard.org.uk';

// find the action variable if present in the page address
$action = $_GET['action'];

// if cookie is set
if (isset($_COOKIE["cookie:mpbcadmin"])) {

	// if a form has been posted
	if ($_POST['submit']) {
		$firstname = $_POST['firstname'];
		$lastname  = $_POST['lastname'];
		$email	   = $_POST['email'];
		$username  = $_POST['username'];
		$password  = $_POST['changepassword'];
		$confirm   = $_POST['confirmpassword'];
		
		if ($_POST['emailLinkup'] == 'on')
			$emailLinkup = 1;
		else
			$emailLinkup = 0;
			
		if ($_POST['emailMessage'] == 'on')
			$emailMessage = 1;
		else
			$emailMessage = 0;
			
		if ($_POST['emailBlog'] == 'on')
			$emailBlog = 1;
		else
			$emailBlog = 0;
		
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
		if ($password <> $confirm) {
			$error5   = 1;
			$error6   = 1;
			$password = NULL;
			$confirm  = NULL;
			$error7   = 1;
			$error    = 1;
		}
		
		//check email address in the form against all the other email addresses in the database and report an error if there is a match
		$query = "SELECT email FROM users WHERE email='$email' AND confirmed <> 2 AND user_id <> '$user_id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	
		if (mysql_num_rows($result) > 0) {
			$error3   = 1;
			$error10  = 1;
			$error    = 1;
			$email = NULL;
		}
		
		//if there were no errors, update the users account with the new details
		if ($error == NULL) {
			$query  = "UPDATE users SET name='$firstname', surname='$lastname', email='$email', ";
			if ($password <> NULL) {
				$query .= "password='$password', ";
				$password = '';
			}
			$query .= "emailLinkup='$emailLinkup', emailMessage='$emailMessage', emailBlog='$emailBlog' WHERE user_id='$user_id'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			$error = 2;
		}
	}
	
	//if the form hasn't been submitted, get the users details from the database ready to display in the form
	else {
		$query  = "SELECT username, name, surname, email, emailLinkup, emailMessage, emailBlog FROM users WHERE user_id = '$user_id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			$username	  = $row['username'];
			$firstname    = $row['name'];
			$lastname	  = $row['surname'];
			$email		  = $row['email'];
			$emailLinkup  = $row['emailLinkup'];
			$emailMessage = $row['emailMessage'];
			$emailBlog 	  = $row['emailBlog'];
		}
	}
	
	if ($action == 'logout') {
		echo '<h3><img src="images/icons/key.png" title="log out" alt="key icon" /> Site log out</h3>';
		echo 'Logging out...';
		echo '<meta http-equiv="refresh" content="0;url=dologin.php">';
	}
	else {
	
		if ($role == 1) {
			echo '<h3>Site Administration</h3>';
			echo '<div class="calendar"><div class="options">';
				echo '<div class="selected"><a href="?action=logout">Log out</a></div>';
			echo '</div></div>';
			echo '<p>Hi ' . $firstname . '. On this page you can manage different areas of the site. To edit content on any page, return to the main site and browse to the page you want to edit. At the bottom of the page, you will find an \'Edit this page\' link that will allow you to edit the content on that page.</p>
      <p>To manage other areas of the site, click a link below:</p>';			
		}
		elseif ($role == 2 || $role == 3 || $role == 4) {
			echo '<h3>My Preferences</h3>';
			echo '<div class="calendar"><div class="options">';
				echo '<div class="selected"><a href="?action=logout">Log out</a></div>';
			echo '</div></div>';
			echo '<p>Welcome, ' . $firstname . '.</p>';
			echo '<p>The members area is still under development. Use this page to alter your account details and preferences. Being a registered user means that you can:</p>';
			echo '<ul><li>View all <a href="'.$PHP_SELF.'?link_up">Link-Up</a> articles, not just public ones.</li></ul>';
			echo '<ul><li>Subscribe to receive email notifications for <a href="'.$PHP_SELF.'?blogs">blog updates</a>.</li></ul>';
			echo '<p>This list will be updated as the site is developed. If you have ideas you would like to see included on the site, or you encounter any problems, please get in touch with us. You can speak to Micky Munroe or Nic Garner at church, or email: <a href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>';
		}
		
		echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'">';
			echo '<div class="inline" style="width:400px;">';
				if ($error == 2)
					echo confirmMessage("Your changes have been saved.");
				echo '<h4>Account details</h4><br/>';
				if ($error == 1) {	
					echo '<div class="error">There was a problem with the information you provided. ';
					echo 'Please check the sections highlighted below.</div>';
				}
				echo '<div class="label">Username:</div> <input name="username" size="30" value="'.$username.'" ';
				echo 'style="background-color:#DDDDDD" disabled="DISABLED"><br class="clear" />';
				echo '<input name="username" type="hidden" value="'.$username.'">';
				if ($error7 == 1) { echo '<br /><div class="error">The passwords you gave do not match. Please try again.</div>'; }
				echo '<div class="label">Change password:</div> <input type="password" name="changepassword" size="30" value="'.$password.'"';
				if ($error5 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Confirm password:</div> <input type="password" name="confirmpassword" size="30" value="'.$password.'"';
				if ($error6 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">First name:</div> <input name="firstname" size=30 value="'.$firstname.'"';
				if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Last name:</div> <input name="lastname" size=30 value="'.$lastname.'"';
				if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				if ($error10 == 1) { echo '<br /><div class="error">This email address is already in use. Please try again.</div><br />'; }
				echo '<div class="label">Email address:</div> <input name="email" size=30 value="'.$email.'"';
				if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br/><br/>';
				echo '<h4>Preferences</h4><br/>';
				echo '<p><input type="checkbox" name="emailLinkup"';
					if ($emailLinkup == 1) {
						echo ' checked="checked"';
					}
				echo '>	Email me when new editions of Link-Up are available.</p>';
				echo '<p><input type="checkbox" name="emailMessage"';
					if ($emailMessage == 1) {
						echo ' checked="checked"';
					}
				echo '>	Email me when new message recordings are available.</p>';
				echo '<p><input type="checkbox" name="emailBlog"';
					if ($emailBlog == 1) {
						echo ' checked="checked"';
					}
				echo '>	Email me when new blogs are created.</p>';
				echo '<p>To edit your email preferences for individual blogs, go to the <a href="'.$PHP_SELF.'?blogs">Blogs</a> page.</p><br>';
				echo '<p class="submit" align="center"><input type="submit" name="submit" value="Save changes"></p>';
				echo '</form>';
			echo '</div>';
      	
			
			
        echo '<div class="inline" style="padding:0 0 30px 20px; width:250px">';
				printadminmenu();
        echo '</div>';
			
				$query  = "SELECT name, shortname FROM content WHERE editor = '$user_id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				if (mysql_num_rows($result) > 0) {
          echo '<div class="inline" style="padding:0 0 0 20px; width:250px">';
					echo '<h4>Editing rights</h4>';
					echo '<p>You have permission to modify the following pages on the site:</p>';
					echo '<ul>';
					while ($row = mysql_fetch_assoc($result))
						echo '<li><a href="'.$row['shortname'].'">'.$row['name'].'</a></li>';
					echo '</ul>';
					echo '<p>To edit a page, go to that page and then click the edit link at the bottom of the page:</p><p><img src="images/graphics/editbutton.jpg" alt="Edit this page"></p>';
          echo '</div>';
				}
			
		}
	}


else {
	if ($action == 'reset') {
		echo '<h3><img src="images/icons/key.png" title="Reset password" alt="key icon" /> Reset password</h3>';
		echo '<p>Please enter the email address you registered with into the box below. You will then recieve an email with a temporary password which will allow you to log back into the site. You will then be required to change this password to a new one that is personal to you.</p>';
		if ($_POST['submit']) {
			$email = $_POST['email'];
			
			if ($email == NULL) {
				$error  = 1;
				$error1 = 1;
			}
			else {
				$query = "SELECT email FROM users WHERE email='$email' AND confirmed <> 2";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
				if (mysql_num_rows($result) == 0) {
					$error   = 1;
					$error1  = 1;
					$error4  = 1;
					$email = NULL;
				}
			}
			
			$privatekey = "6LcuLQwAAAAAAOnPdFbePk2ucZuWM6WK3d0QTNRX";
			$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
				$error3 = 1;
				$error  = 1;
			}
			
			if ($error == 1) {
				echo '<div class="error">There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</div>';
			}
			else {
				$query = "SELECT user_id, name FROM users WHERE email = '$email'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				while ($row = mysql_fetch_assoc($result)) {
					$user_id = $row['user_id'];
					$firstname   = $row['name'];
				}
				
				$tempPassword = createPassword();
				
				$query = "UPDATE users SET confirmed='3', password='$tempPassword' WHERE user_id='$user_id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$message = $emailHead.'	
						
				<p>Hi '.$firstname.',</p>
						
				<p>This email is in response to your password reminder request from the Mount Pleasant Baptist Church website on 
				'.date('F j Y').'. You have been issued a temporary password, which is: '.$tempPassword.'</p>
				
				<p>Next time you log in to the site, use this password. When you log in, you will be required to change the temporary 
				password to a new one that is personal to you. Please go to <a href="http://www.mountpleasantchurch.com/'.$page.'">
				http://www.mountpleasantchurch.com/'.$page.'</a> to log in.</p>
						
				<p>If you believe you\'ve received this email in error, or you have any questions about this service, please email <a 
				href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>
						
				<p>The Mount Plesant Website Team</p></body></html>';
			
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";
				
				mail($email, 'Mount Pleasant Baptist Church password reset', $message, $headers);
				
				echo confirmMessage("Thank you, your details have been submitted. You should receive an email soon with 
				your temporary password.");
				echo '<br />';
			}
		}
		else {
			$error = 1;
		}
		if ($error <> NULL) {
			echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'&action=reset">';
				if ($error4 == 1) { echo '<br /><div class="error">The email address you entered could not be found. Please try again.</div>'; }
				echo '<div class="label" style="width:143px">Email address:</div> <input name="email" size=30 value="'.$email.'"';
				if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';

				if ($error3 == 1) { echo '<br /><div class="error">Please type the two words below into the box.</div>'; }				
				$publickey = "6LcuLQwAAAAAAGahrjcGmrOvVLyp7jHRFKEJegtK";
				echo '<div style="margin-left:54px">';
					echo recaptcha_get_html($publickey, null, !empty($_SERVER['HTTPS']));
				echo '</div>';
				
				echo '<br class="clear" /><br />';
				echo '<p class="submit"><input type="submit" name="submit" value="submit"> <input type="reset" name="reset" value="reset"></p>';
			echo '</form>';
		}
	}
	elseif ($action == 'register') {
		echo '<h3><img src="images/icons/key.png" title="Register" alt="key icon" /> Register</h3>';
		echo '<p>Registration is required to access certain areas of this website. Site membership is availiable to all church members and regular members of the congregation.</p><p>To register, please fill in your details below. These will be reviewed by one of the ministers before your membership is activated, and you will receive an email to confirm. If you do not have an email address but want to register, please speak to Micky Munroe at church.</p>';
		if ($_POST['submit']) {
			$firstname = $_POST['firstname'];
			$lastname  = $_POST['lastname'];
			$email	   = $_POST['email'];
			$username  = $_POST['username'];
			$password  = $_POST['password'];
			$confirm   = $_POST['confirm'];
			
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
			if ($username == NULL) {
				$error4 = 1;
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
			
			$privatekey = "6LcuLQwAAAAAAOnPdFbePk2ucZuWM6WK3d0QTNRX";
			$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
				$error9 = 1;
				$error  = 1;
			}
		
			$query = "SELECT username FROM users WHERE username='$username' AND confirmed <> 2";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
			if (mysql_num_rows($result) > 0) {
				$error4   = 1;
				$error8   = 1;
				$error    = 1;
				$username = NULL;
			}
			
			if ($email <> NULL) {
				$query = "SELECT email FROM users WHERE email='$email' AND confirmed <> 2";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
				if (mysql_num_rows($result) > 0) {
					$error3   = 1;
					$error10  = 1;
					$error    = 1;
					$email = NULL;
				}
			}
			
			if ($error == 1) {
				echo '<div class="error">There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</div>';
			}
			else {
				$query = "INSERT INTO users (username, name, surname, email, password) VALUES ('$username', '$firstname', '$lastname', '$email', '$password')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				
$message = $emailHead.'

<p>Hi,</p>
		
<p>A new user ('.$username.') has registered on the Mount Pleasant Baptist Church website. The new account needs approving before it can be used. Please log in to the site and confirm or deactivate this account at your earliest convenience:</p>

<p><a href="http://www.mountpleasantchurch.com/'.$page.'">http://www.mountpleasantchurch.com/'.$page.'</a></p>
		
<p>If you believe you\'ve received this email in error, or you have any questions about this service, please email <a href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>
		
<p>Have a nice day!</p></body></html>';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Mount Plesant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";

mail($user_admin, 'Mount Plesant Baptist Church account registration', $message, $headers);
				
				echo confirmMessage("Thank you, your details have been submitted. You should receive an email within the next few days to confirm your membership.");
				echo '<br />';
				
				
			}
		}
		else {
			$error = 1;
		}
		if ($error <> NULL) {
			echo '<div class="inline" style="width:400px">';
			echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'&action=register">';
				echo '<h4>Your personal details</h4><br />';
				echo '<div class="label">First name:</div> <input name="firstname" size=30 value="'.$firstname.'"';
				if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Last name:</div> <input name="lastname" size=30 value="'.$lastname.'"';
				if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				if ($error10 == 1) { echo '<br /><div class="error">This email address is already in use. Please try again.</div>'; }
				echo '<div class="label">Email address:</div> <input name="email" size=30 value="'.$email.'"';
				if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<h4>Your account details</h4><br />';
				if ($error8 == 1) { echo '<div class="error">The username you chose is not availaible. Please try again.</div>'; }
				echo '<div class="label">Username:</div> <input name="username" size=30 value="'.$username.'"';
				if ($error4 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				if ($error7 == 1) { echo '<br /><div class="error">The passwords you gave do not match. Please try again.</div>'; }
				echo '<div class="label">Password:</div> <input type="password" name="password" size=30" value="'.$password.'"';
				if ($error5 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" />';
				echo '<div class="label">Confirm password:</div> <input type="password" name="confirm" size=30" value="'.$confirm.'"';
				if ($error6 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br class="clear" /><br />';
				
				if ($error9 == 1) { echo '<br /><div class="error">Please type the two words below into the box.</div>'; } 
				
				$publickey = "6LcuLQwAAAAAAGahrjcGmrOvVLyp7jHRFKEJegtK";
				echo '<div style="margin-left:20px">';
					echo recaptcha_get_html($publickey, null, !empty($_SERVER['HTTPS']));
				echo '</div>';
				
				echo '<br class="clear" /><br />';
				echo '<p class="submit" align="center"><input type="submit" name="submit" value="submit"> <input type="reset" name="reset" value="reset"></p>';
			echo '</form>';
			
			echo '</div><div class="inline" style="text-align:center; width:230px; "><br /><br /><br /><br /><p><font size="4">Already registered?</p><p>Click <a href="'.$page.'">here</a> to log in.</font></p></div>';
		}
	}
	
	// log in
	else {
		echo '<h3><img src="images/icons/key.png" title="log in" alt="key icon" /> Site log in</h3>';
		if ($_POST['submit']) {
			$name = $_POST['name'];
			$pass = $_POST['pass'];
			
			if ($name != null) {
				$query = "SELECT user_id, username, password, confirmed FROM users WHERE username='$name'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			}
			else {
				echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'">';
				echo '<p>Type in your username and password below to log in to the site.</p>';
				if (array_key_exists('target', $_POST))
					echo '<input type="hidden" name="target" value="'.$_POST['target'].'" />';
				echo '<div class="error">Please enter your username and password.</div>';
					echo '<table border=0>';
						echo '<tr><td>Username: </td><td><input name="name" size=20></td></tr>';
						echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20> &nbsp;&nbsp;';
						echo ' <a href="'.$page.'?action=reset">I\'ve forgotten my password.</a></td></tr>';
						echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
					echo '</table>';
				echo '</form>';
			}
		
			if (mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_assoc($result)) {
					if ($row['confirmed'] == 1 || $row['confirmed'] == 3) {
						if ($pass == $row['password']) {
							echo '<p>Logging in...</p>';
							$querystring = 'id=' . $row['user_id'];
							if (array_key_exists('target', $_POST))
								$querystring .= '&target=' . $_POST['target'];  
							echo '<meta http-equiv="refresh" content="0;url=dologin.php?'.$querystring.'">';
						}
						else {
							echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'">';
							echo '<p>Type in your username and password below to log in to the site.</p>';
							echo '<div class="error">The password you entered is not correct for this username. Please try again.</div>';
							if (array_key_exists('target', $_POST))
								echo '<input type="hidden" name="target" value="'.$_POST['target'].'" />';
								echo '<table border=0>';
									echo '<tr><td>Username: </td><td><input name="name" size=20 value="' . $name . '"></td></tr>';
									echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20> &nbsp;&nbsp;';
									echo '<a href="'.$page.'?action=reset">I\'ve forgotten my password.</a></td></tr>';
									echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
								echo '</table>';
							echo '</form>';
						}
					}
					else {
						echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'">';	
						echo '<p>Type in your username and password below to log in to the site.</p>';
						echo '<div class="error">We\'re sorry, your account isn\'t active at the moment. Please contact <br/>Micky Munroe at church if you think this is in error.</div>';
							echo '<table border=0>';
								echo '<tr><td>Username: </td><td><input name="name" size=20 value="' . $name . '"></td></tr>';
								echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20> &nbsp;&nbsp;';
								echo ' <a href="'.$page.'?action=reset">I\'ve forgotten my password.</a></td></tr>';
								echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
							echo '</table>';
						echo '</form>';
					}
				}
			}
			else {
				echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'">';
				echo '<p>Type in your username and password below to log in to the site.</p>';
				if (array_key_exists('target', $_POST))
					echo '<input type="hidden" name="target" value="'.$_POST['target'].'" />';
				echo '<div class="error">The username you entered could not be found. Please try again.</div>';
					echo '<table border=0>';
						echo '<tr><td>Username: </td><td><input name="name" size=20></td></tr>';
						echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20> &nbsp;&nbsp;';
						echo ' <a href="'.$page.'?action=reset">I\'ve forgotten my password.</a></td></tr>';
						echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
					echo '</table>';
				echo '</form>';
			}
		}
		
		else {
			echo '<form method="post" action="' . $PHP_SELF . '?'.$page.'">';
				echo '<p>Type in your username and password below to log in to the site.</p>';
				$target = urldecode($_GET['target']);
				if ($target) echo '<input type="hidden" name="target" value="'.$target.'" />';
				echo '<table border=0>';
					echo '<tr><td>Username: </td><td><input name="name" size=20></td></tr>';
					echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20> &nbsp;&nbsp;';
						echo ' <a href="'.$page.'?action=reset">I\'ve forgotten my password.</a></td></tr>';
					echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
				echo '</table>';
			echo '</form>';
		}
		
		echo '<p>Not a member of the site yet? Click <a href="'.$page.'?action=register">here</a> to register.</p>';
	}
}
   
   ?>
