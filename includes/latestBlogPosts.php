<?php 

	global $page;
	
	$query  = "SELECT blog_posts.blog_post_id, DATE_FORMAT(blog_posts.date, '%W %M %D %Y') as date, blog_posts.title, ";
	$query .= "blogs.blog_id, blogs.name, blogs.image, blogs.image_thumb, blogs.image_caption ";
	$query .= "FROM blog_posts, blogs ";
	$query .= "WHERE blog_posts.date > DATE_SUB(NOW(), INTERVAL 2 MONTH) AND blog_posts.deleted = 0 AND type = 0 AND blog_posts.blog_id = blogs.blog_id ";
	$query .= "ORDER BY blog_posts.date DESC ";
	$query .= "LIMIT 1";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());

	echo '<div style="float:left; margin-left:30px;">';

	while($row = mysql_fetch_object($result)) {
		if (! $row->image == NULL || ! $row->image_thumb == NULL) {
			echo '<a href="'.$PHP_SELF.'?blogs&blog='.$row->blog_id.'&post='.$row->blog_post_id.'">';
			echo '<img class="blog_thumbimg" style="margin:10px 0 20px;" src="images/graphics/';
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
		echo '<div class="blog_thumbdescription" style="margin:10px 0 20px;"><div align="center">';
		echo '<p><font size="2">Latest blog post from <strong>'.$row->name.'</strong></p>';
		echo '<p><font size="5"><strong><a href="'.$PHP_SELF.'?blogs&blog='.$row->blog_id.'&post='.$row->blog_post_id.'">';
		echo $row->title.'</a></strong></font></p>';
		echo '<p>'.$row->date.'</font></p>';
		echo '</div></div>';
	}
	
	echo '</div>';
	

	

?>
