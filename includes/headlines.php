<?php 

	global $page;
	$today = date('Y-m-d');

	$query  = "SELECT headlines.headline, headlines.text, headlines.image, headlines.image_caption, content.shortname ";
    $query .= "FROM headlines LEFT JOIN content ON headlines.content_id = content.content_id ";
	$query .= "WHERE headlines.deleted = 0 ";
	$query .= "AND DATE_FORMAT(start_date, '%Y-%m-%d') <= '$today' AND DATE_FORMAT(end_date, '%Y-%m-%d') > '$today' ";
	$query .= "ORDER BY DATE_FORMAT(start_date, '%Y-%m-%d') DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());


/*
	if (mysql_num_rows($result) > 0) {
		echo '<div class="news">';
		echo "<h3>&nbsp;Latest news from Mount Pleasant</h3>";
		while($row = mysql_fetch_object($result)) {
			if (! $row->image == NULL) {
				echo '<a href="'.$PHP_SELF.'?'.$row->shortname.'">';
				echo '<img width="100" height="100" src="images/graphics/news/'.$row->image.'"';
				if (! $row->image_caption == NULL)
					echo ' alt="'.$row->image_caption.'" title="'.$row->image_caption.'" ';
				else
					echo ' alt="'.$row->name.'" title="'.$row->name.'" ';
				echo ' /></a>';
				echo '<div class="blog_thumbdescription" style="width:400px; font-size:90%; margin-bottom:0;">';
			}
			else 
				echo '<div class="blog_thumbdescription" style="width:510px; font-size:90%; margin-bottom:0;">';
			
			echo '<p><font size="4"><strong><a href="'.$PHP_SELF.'?'.$row->shortname.'">'.$row->headline.'</a></strong></font></p>';
			echo '<p>'.$row->text.'</p>';
			echo '</div>';
		}
		echo '</div>';
	}
*/
	
	if (mysql_num_rows($result) > 0) {
		echo '<div class="ad"><div class="tl" style="height:35px;"></div><div class="tm" style="height:35px;">';
		echo "<h4>Latest news</h4>";
		echo '</div><div class="tr" style="height:35px;"></div><br><div class="mm">';
		while($row = mysql_fetch_object($result)) {
			if (! $row->image == NULL) {
				if (! $row->shortname == NULL)
					echo '<a href="'.$PHP_SELF.$row->shortname.'">';
				echo '<img src="images/graphics/news/'.$row->image.'"';
				if (! $row->image_caption == NULL)
					echo ' alt="'.$row->image_caption.'" title="'.$row->image_caption.'" ';
				else
					echo ' alt="'.$row->name.'" title="'.$row->name.'" ';
				echo ' />';
				if (! $row->shortname == NULL)
					echo '</a>';
				echo '<div class="blog_thumbdescription" style="width:400px; font-size:100%; padding:5px 0 0 0; margin:0;">';
			}
			else 
				echo '<div class="blog_thumbdescription" style="width:520px; font-size:100%; padding:5px 0 0 0; margin:0;">';
			
			echo '<p><font size="4"><strong>';
			if (! $row->shortname == NULL)
				echo '<a href="'.$PHP_SELF.$row->shortname.'">'.$row->headline.'</a>';
			else
				echo $row->headline;
			echo '</strong></font></p>';
			echo '<p style="padding:4px 0 0 0;">'.$row->text.'</p>';
			echo '</div>';
		}
		echo '</div><div class="bl"></div><div class="bm"></div><div class="br"></div></div>';
	}
	

?>
