<?php 	
	include '../connect.php'; 
	include '../functions.php'; 
	$blog_id = $_GET['blog_id'];
	$user_id = $_GET['user_id'];

	$query = "SELECT * FROM blog_subscriptions WHERE user_id = '$user_id' AND blog_id = '$blog_id'";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	if (mysql_num_rows($result) > 0) {
		$query = "DELETE FROM blog_subscriptions WHERE user_id = '$user_id' AND blog_id = '$blog_id'";
		$result = mysql_query($query) or die ('<div class="error">Sorry, something has gone wrong. Your settings have not been saved. Please try again later.</div>');
		echo confirmMessage("Subscription disabled.");
	}
	else {
		$query  = "INSERT INTO blog_subscriptions (blog_id, user_id) VALUES ('$blog_id', '$user_id')";
		$result = mysql_query($query) or die ('<div class="error">Sorry, something has gone wrong. Your settings have not been saved. Please try again later.</div>');
		echo confirmMessage("Subscription enabled.");
	}
?>