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
	
	if ($name == NULL && $email == NULL && $home == NULL && $mobile == NULL) {
		$details_error = 'Please enter your name and a way for us to contact you.';
		$error = 1;
	}
	elseif ($name <> NULL) {
		if ($email == NULL && $home == NULL && $mobile == NULL) {
			$details_error = 'Please enter a way for us to contact you.';
			$error = 1;
		}
	}
	if ($message == NULL) {
		$message_error = 'Please enter a message in the box below to let us know how we can be of help to you.';
		$error = 1;
	}
	
	if ($error == NULL && $captcha_error == NULL) {
		$reply = '

<html>
<body>		
		
<p>Hi,</p>
		
<p>This email has been sent automatically from the House Groups page on the Mount Pleasant Baptist Church website because someone has filled in the form on that page. The details they provided are as follows:</p>
<ul>
<li>Name: '.$name.'</li>
<li>Email: <a href="mailto:'.$email.'">'.$email.'</a></li>
<li>Home number: '.$home.'</li>
<li>Mobile number: '.$mobile.'</li>
<li>Message:<br />'.nl2br($message).'</li>
</ul>
		
<p>If you believe you\'ve received this email in error, or you have any questions about this service, please email <a href="mailto:webmaster@mountpleasantchurch.com">webmaster@mountpleasantchurch.com</a></p>
		
<p>Have a nice day!</p>
';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";

		mail('housegroups@mountpleasantchurch.com', 'House Groups contact form notification', $reply, $headers);
		echo '<div class="confirm">Thank you, your message has been sent.</div>';
	}
}

?>
	<a name="form"></a>
    <form name="contact_form" method="post" action="<?php echo $PHP_SELF; ?>#form">
        <h4>Your details</h4>
		<p>Let us know how we can contact you.</p>
        <?php 
        	if ($details_error <> NULL) {
                echo '<div class="error">' . $details_error . '</div><br />';
            }
        ?>
        <input type="hidden" name="form" value="contact_form" />
        
        <div class="label">Name:</div>
        <input name="name" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $name; } ?>" /><br class="clear" />
        <div class="label">Email address:</div>
        <input name="email" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $email; } ?>" /><br class="clear" />
        <div class="label">Home number:</div>
        <input name="home" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $home; } ?>" /><br class="clear" />
        <div class="label">Mobile number:</div>
        <input name="mobile" size="30" maxlength="60" value="<?php if ($error <> NULL || $captcha_error <> NULL) { echo $mobile; } ?>"><br class="clear" /><br />
        
        <h4>How can we help you?</h4>
        <p>Please let us know how we can help you.</p>
        <?php 
        	if ($message_error <> NULL || $captcha_error <> NULL) {
                echo '<div class="error">' . $message_error . '</div><br />';
            }
        ?>
        <textarea name="message" cols="60" rows="5"><?php if ($error <> NULL) { echo stripslashes($message); } ?></textarea><br /><br />
        <?php
			// only show error message for the captcha if other fields are filled in correctly
			if ($captcha_error <> NULL && $error == NULL)
                echo '<div class="error">' . $captcha_error . '</div><br />';
			$publickey = "6LcuLQwAAAAAAGahrjcGmrOvVLyp7jHRFKEJegtK";
			echo recaptcha_get_html($publickey, null, !empty($_SERVER['HTTPS']));
		?>
        <br />
        <input type="submit" value="Send" class="submit" name="submit"><br class="clear" /><br />
        
    </form>
