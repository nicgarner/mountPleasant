<?php

global $page;
global $username;
global $role;
global $ip;

# set email address of person to administer these accounts here:
$user_admin = 'me@nicgarner.co.uk';

$action = $_GET['action'];

if (isset($_COOKIE["cookie:mpbcadmin"])) {
	if ($action == 'logout') {
		echo '<h3><img src="../images/icons/key.png" title="login" alt="key icon" /> Site Logout</h3>';
		echo 'Logging out...';
		echo '<meta http-equiv="refresh" content="0;url=loginnow.php">';
	}
	elseif ($role == 1) {
		echo '<h3>Site Administration</h3>';
		echo '<p>Hi ' . $username . '. On this page you can manage different areas of the site. To edit content on any page, return to the main site and browse to the page you want to edit. At the bottom of the page, you will find an \'Edit this page\' that will allow you to edit the content on that page.</p><p>To manage other areas of the site, click a link below (not all pages work yet):</p>';
		
		printadminmenu();
		
	}
	elseif ($role == 2) {
		echo '<h3>Members Page</h3>';
		echo '<p>You are logged in as ' . $username . '. Click <a href="'.$PHP_SELF.'?'.$page.'&action=logout">here</a> to logout.</p>';
		echo '<p>The members area is still under development. Being a registered user means that you can:</p>';
		echo '<ul><li>View all <a href="'.$PHP_SELF.'?link_up">Link-Up</a> articles, not just public ones.</li></ul>';
		echo '<p>This list will be updated as the site is developed. If you have ideas you would like to see included on the site, or you encounter any problems, please get in touch with us. You can speak to Micky Munroe or Nic Garner at church, or email: <a href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>';
	}
	elseif ($role == 3) {
		echo '<h3>Members Page</h3>';
		echo '<p>You are logged in as ' . $username . '. Click <a href="'.$PHP_SELF.'?'.$page.'&action=logout">here</a> to logout.</p>';
		echo '<p>The members area is still under development. Being a registered user means that you can:</p>';
		echo '<ul><li>View all <a href="'.$PHP_SELF.'?link_up">Link-Up</a> articles, not just public ones.</li></ul>';
		echo '<p>This list will be updated as the site is developed. If you have ideas you would like to see included on the site, or you encounter any problems, please get in touch with us. You can speak to Micky Munroe or Nic Garner at church, or email: <a href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>';
	}
	
}

else {
	
	if ($action == 'register') {
		echo '<h3><img src="../images/icons/key.png" title="login" alt="key icon" /> Register</h3>';
		echo '<p>Registration is required to access certain areas of this website. Site membership is availiable to all church members and regular members of the congregation.</p><p>To register, please fill in your details below. These will be reviewed by one of the ministers before your membership is activated, and you will receive an email to confirm. If you do not have an email address but want to register, please speak to Micky Munroe at church.</p>';
		if ($_POST['submit']) {
			$firstname = $_POST['firstname'];
			$lastname  = $_POST['lastname'];
			$email	   = $_POST['email'];
			$username  = $_POST['username'];
			$password  = $_POST['password'];
			$confirm   = $_POST['confirm'];
			$chicken   = $_POST['chicken'];
			
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
			if ($chicken <> $_SESSION['captcha']) {
				$error   = 1;
				$error9  = 1;
				$chicken = NULL;
			}
		
			$query = "SELECT username FROM users WHERE username='$username'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
			if (mysql_num_rows($result) > 0) {
				$error4   = 1;
				$error8   = 1;
				$error    = 1;
				$username = NULL;
			}
			
			if ($error == 1) {
				echo '<div class="error">There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</div><br />';
			}
			else {
				$query = "INSERT INTO users (username, name, surname, email, password) VALUES ('$username', '$firstname', '$lastname', '$email', '$password')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				
$message = '

<html>
<body>		
		
<p>Hi,</p>
		
<p>A new user has registered on the Mount Pleasant Baptist Church website. The new account needs approving before it can be used. Please log in to the site and confirm or deactivate this account:</p>

<p><a href="http://www.mountpleasantchurch.com/'.$page.'">http://www.mountpleasantchurch.com/'.$page.'</a></p>
		
<p>If you believe you\'ve received this email in error, or you have any questions about this service, please email <a href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>
		
<p>Have a nice day!</p>
';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Mount Plesant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";

mail($user_admin, 'Mount Plesant Baptist Church account registration', $message, $headers);
				
				echo '<br /><div class="confirm"><p>Thank you, your details have been submitted. You should receive an email within the next few days to confirm your membership.</p></div><br />'; 
			}
		}
		else {
			$error = 1;
		}
		if ($error <> NULL) {
			echo '<div class="inline" style="width:400px">';
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?'.$page.'&action=register">';
				echo '<h4>Your personal details</h4><br />';
				echo '<div class="label">First name:</div> <input name="firstname" size=30 value="'.$firstname.'"';
				if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br />';
				echo '<div class="label">Last name:</div> <input name="lastname" size=30 value="'.$lastname.'"';
				if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br />';
				echo '<div class="label">Email address:</div> <input name="email" size=30 value="'.$email.'"';
				if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br />';
				echo '<h4>Your account details</h4><br />';
				if ($error8 == 1) { echo '<br /><div class="error">The username you chose is not availaible. Please try again.</div>'; }
				echo '<div class="label">Username:</div> <input name="username" size=30 value="'.$username.'"';
				if ($error4 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br />';
				if ($error7 == 1) { echo '<br /><div class="error">The passwords you gave do not match. Please try again.</div>'; }
				echo '<div class="label">Password:</div> <input type="password" name="password" size=30" value="'.$password.'"';
				if ($error5 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br />';
				echo '<div class="label">Confirm password:</div> <input type="password" name="confirm" size=30" value="'.$confirm.'"';
				if ($error6 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br /><br />';
				
				if (empty($_SESSION['captcha'])) {
					$_SESSION['captcha'] = createCaptcha();
				}
				
				echo '<div class="label" style="width:143px"></div><font size="4">'.$_SESSION['captcha'].'</font><br />';
				echo '<div class="label" style="width:143px">Enter the code above:</div> <input name="chicken" size="10" value="'.$chicken.'"';
				if ($error9 == 1) { echo ' style="background-color:#FFAFA4"'; } echo '><br /><br />';
				echo '<p class="submit" align="center"><input type="submit" name="submit" value="submit"> <input type="reset" name="reset" value="reset"></p>';
			echo '</form>';
			
			echo '</div><div class="inline" style="text-align:center; width:230px"><br /><br /><br /><br /><font size="4">Already registered?</p><p>Click <a href="'.$_SERVER['PHP_SELF'].'?'.$page.'">here</a> to log in.</font></p></div>';
		}
	}
	else {
		echo '<h3><img src="../images/icons/key.png" title="login" alt="key icon" /> Site Login</h3>';
		if ($_POST['submit']) {
			$name = $_POST['name'];
			$pass = $_POST['pass'];
		
			$query = "SELECT user_id, username, password, confirmed FROM users WHERE username='$name'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
			if (mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_assoc($result)) {
					if ($row['confirmed'] == 1) {
						if ($pass == $row['password']) {
							echo '<p>Logging in...</p>';
							echo '<meta http-equiv="refresh" content="0;url=loginnow.php?id='.$row['user_id'].'">';
						}
						else {
							echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?'.$page.'">';
							echo '<p>Type in your username and password below to log in to the site.</p>';
							echo '<p><div class="error">The password you entered is not correct for this username. Please try again.</div></p>';
								echo '<table border=0>';
									echo '<tr><td>Username: </td><td><input name="name" size=20 value="' . $name . '"></td></tr>';
									echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20></td></tr>';
									echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
								echo '</table>';
							echo '</form>';
						}
					}
					else {
						echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?'.$page.'">';
						echo '<p>Type in your username and password below to log in to the site.</p>';
						echo '<p><div class="error">We\'re sorry, your account isn\'t active at the moment. Please contact Micky Munroe at church if you think this is in error.</div></p>';
							echo '<table border=0>';
								echo '<tr><td>Username: </td><td><input name="name" size=20 value="' . $name . '"></td></tr>';
								echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20></td></tr>';
								echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
							echo '</table>';
						echo '</form>';
					}
				}
			}
			else {
				echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?'.$page.'">';
				echo '<p>Type in your username and password below to log in to the site.</p>';
				echo '<p><div class="error">The username you entered could not be found. Please try again.</div></p>';
					echo '<table border=0>';
						echo '<tr><td>Username: </td><td><input name="name" size=20></td></tr>';
						echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20></td></tr>';
						echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
					echo '</table>';
				echo '</form>';
			}
		}
		
		else {
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?'.$page.'">';
				echo '<p>Type in your username and password below to log in to the site.</p>';
				echo '<table border=0>';
					echo '<tr><td>Username: </td><td><input name="name" size=20></td></tr>';
					echo '<tr><td>Password: </td><td><input type="password" name="pass" size=20></td></tr>';
					echo '<tr><td></td><td><p class="submit"><input type="submit" name="submit" value="submit"></p></td></tr>';
				echo '</table>';
			echo '</form>';
		}
		
		echo '<p>Not a member of the site yet? Click <a href="/?'.$page.'&action=register">here</a> to register.</p>';
	}
}
   
   ?>
