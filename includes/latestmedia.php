<?php 

global $page;
	
echo '<div class="latestmedia">';
	
	//get latest sunday messages	
	$query  = "SELECT recordings.name as name, speaker, DATE_FORMAT(date, '%W %D %M %Y') as datef, TIME_FORMAT(date, '%l.%i %p') as time, ";
	$query .= "recordings_categories.name as category, shortname ";
	$query .= "FROM recordings LEFT JOIN recordings_categories ON recordings.category = recordings_categories.recordings_category_id ";
#	$query .= "WHERE DATE_FORMAT(date, '%Y%m%d') <= $lastSunday AND DATE_FORMAT(date, '%Y%m%d') > $firstSunday ";
	$query .= "WHERE deleted = '0' ";
	$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i') LIMIT 2";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	if (mysql_num_rows($result) > 0) {
		echo '<h4>Latest Sunday messages</h4>';
		while($row = mysql_fetch_object($result)) {
			echo '<p class="small">';
			echo '<strong><a href="sunday_messages">'.$row->name.'</a></strong> by '.$row->speaker.'<br/>';
			if (!$row->category == NULL)
				echo 'Series: <a href="sunday_messages?series='.$row->shortname.'">'.$row->category.'</a><br/>';
			echo '<span class="light">'.$row->datef.' '.$row->time;
			echo '</p>';
		}
		echo '<div class="rule"></div>';
	}
		
	//get latest blog post
	$query  = "SELECT blog_posts.blog_post_id, DATE_FORMAT(blog_posts.date, '%W %M %D %Y') as date, blog_posts.title, ";
	$query .= "blogs.blog_id, blogs.name, blogs.image, blogs.image_thumb, blogs.image_caption ";
	$query .= "FROM blog_posts, blogs ";
	$query .= "WHERE blog_posts.deleted = 0 AND type = 0 AND blog_posts.blog_id = blogs.blog_id ";
	$query .= "ORDER BY blog_posts.date DESC ";
	$query .= "LIMIT 1";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)) {
			if (! $row->image == NULL || ! $row->image_thumb == NULL) {
				echo '<h4>Latest blog post</h4>';
				echo '<a href="blogs?blog='.$row->blog_id.'&post='.$row->blog_post_id.'">';
				echo '<img src="images/graphics/';
				if ($row->image_thumb == NULL)
					echo $row->image;
				else
					echo $row->image_thumb;
				echo '"';
				if (! $row->image_caption == NULL)
					echo ' alt="'.$row->image_caption.'" title="'.$row->image_caption.'" ';
				else
					echo ' alt="'.$row->name.'" title="'.$row->name.'" ';
				echo ' /></a>';
			}
			echo '<div>';
			echo '<h5><a href="blogs?blog='.$row->blog_id.'&post='.$row->blog_post_id.'">'.$row->title.'</a></h5>';
			echo '<p class="small">'.$row->name.'<br/>'.$row->date.'</p></div>';
		}
		echo '<div class="rule"></div>';
	}
	
echo '</div>';
	

	

?>
