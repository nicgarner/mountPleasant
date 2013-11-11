<div class="footer">
All content copyright Mount Pleasant Baptist Church 2006 - <? echo date('Y'); ?>
<?php
	global $role;
	global $fullname;
	if (isset($_COOKIE["cookie:mpbcadmin"])) {
		echo ' | <img src="/images/icons/user.png" title="" alt="user icon" />';
		echo ' You are logged in as ' . $fullname;
		if ($role == 1)
			echo ' | <a href="/login">Administration</a>';
		if ($role == 2 || $role == 3)
			echo ' | <a href="/login">My Preferences</a>';
		echo ' | <a href="/login?action=logout">Log out</a>';
	}
	else
		echo ' | <a href="/login">Log in</a>';
?>	
</div>

