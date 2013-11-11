<?php

include 'includes/recaptchalib.php';

if ($_POST['submit'] && $_POST['form'] == 'contact_form'){
	$confirm = $_POST['confirm'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$home = $_POST['home'];
	$mobile = $_POST['mobile'];
	$message = strip_tags($_POST['message']);
	
	$privatekey = "6LcuLQwAAAAAAOnPdFbePk2ucZuWM6WK3d0QTNRX";
	$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);
	if (!$resp->is_valid) {
		$captcha_error = 'Please type the two words below into the box.';
	}
	
	if ($email == NULL && $home == NULL && $mobile == NULL && $message <> NULL) {
		if ($confirm == '0') {
			$confirm = '1';
			$details_error = 'You didn\'t enter any contact details. If that is what you want, click Send again to send us your message anonymously.';
			$error = 1;
		}
	}
	if ($message == NULL) {
		$message_error = 'Please enter a message in the box below.';
		$error = 1;
	}
	
	if ($error == NULL && $captcha_error == NULL) {
		$date = date("Y-m-d G:i:s");
		$query = "INSERT INTO contact_form(name, email, home, mobile, message, time) VALUES ('$name', '$email', '$home', '$mobile', '$message', '$date')";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		$reply = '

			<html>
			<body>		
					
			<p>Hi,</p>
					
			<p>This email has been sent automatically from the Contact Form page on the Mount Pleasant Baptist Church 
			website because someone has filled in the form on that page. The details they provided are as follows:</p>
			<ul>
			<li>Name: '.$name.'</li>
			<li>Email: <a href="mailto:'.$email.'">'.$email.'</a></li>
			<li>Home number: '.$home.'</li>
			<li>Mobile number: '.$mobile.'</li>
			<li>Message:<br />'.nl2br($message).'</li>
			</ul>
					
			<p>Have a nice day!</p>
		';

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";

		mail('webmaster@mountpleasantchurch.com', 'Mount Pleasant contact form notification', $reply, $headers);		
		echo '<div class="confirm">Thank you, your message has been sent.</div>';

	}
}

?>
    <form name="contact_form" method="post" action="<?php echo $PHP_SELF; ?>">
        <h4>Your details</h4>
		<p>All of these fields are optional. If you want us to be able to contact you, please leave a way for us to do this.</p>
        <?php 
        	if ($details_error <> NULL)
                echo '<div class="error">' . $details_error . '</div><br />';
        ?>
        <input type="hidden" name="form" value="contact_form" />
        <input type="hidden" name="confirm" value="<?php if ($confirm == '1') { echo '1'; } else {echo '0'; } ?>" />
        
        <div class="label">Name:</div>
        <input name="name" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $name; } ?>" />
        <br class="clear" />
        <div class="label">Email address:</div>
        <input name="email" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $email; } ?>" />
        <br class="clear" />
        <div class="label">Home number:</div>
        <input name="home" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $home; } ?>" />
        <br class="clear" />
        <div class="label">Mobile number:</div>
        <input name="mobile" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $mobile; } ?>">
        <br class="clear" /><br />
        
        <h4>How can we help you?</h4>
        <p>Please let us know how we can help you, practically or through prayer.</p>
        <?php 
        	if ($message_error <> NULL)
                echo '<div class="error">' . $message_error . '</div><br />';
        ?>
        <textarea name="message" cols="70" rows="6"><?php if ($error <> NULL || $captcha_error <> NULL) { echo stripslashes($message); } ?></textarea><br /><br />
        <?php
			// only show error message for the captcha if other fields are filled in correctly
			if ($captcha_error <> NULL && $error == NULL)
                echo '<div class="error">' . $captcha_error . '</div><br />';
			$publickey = "6LcuLQwAAAAAAGahrjcGmrOvVLyp7jHRFKEJegtK";
			echo recaptcha_get_html($publickey, null, !empty($_SERVER['HTTPS']));
		?>
        <br />
        <input type="submit" value="Send" class="submit" name="submit">
        
    </form>
