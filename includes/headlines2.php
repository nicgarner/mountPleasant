<?php 

	global $page;
	$today = date('Y-m-d');

	$startAd = $_GET['ad'];
	if($startAd == NULL)
		$startAd = 1;
	echo '<script>showingad='.$startAd.';</script>';
	$query  = "SELECT headlines.headline, headlines.text, headlines.image, headlines.image_caption, content.shortname ";
    $query .= "FROM headlines LEFT JOIN content ON headlines.content_id = content.content_id ";
	$query .= "WHERE headlines.deleted = 0 ";
	$query .= "AND DATE_FORMAT(start_date, '%Y-%m-%d') <= '$today' AND DATE_FORMAT(end_date, '%Y-%m-%d') > '$today' ";
	$query .= "ORDER BY DATE_FORMAT(end_date, '%Y-%m-%d') ASC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());

	$delay = '6000';
	
	if (mysql_num_rows($result) > 0) {
		echo '<div class="headlines" id="banner_ads" onmouseover="stopSwitchHeadlines()" onmouseout="resumeSwitchHeadlines('.mysql_num_rows($result).',showingad,'.$delay.')">';
		echo '<div class="left"></div>';
		echo '<div class= "middle">';
		$i = 1;
		while($row = mysql_fetch_object($result)) {
			echo '<div id="headline'.$i.'"'; # open containter for ad and nav
				if($i <> $startAd)
					echo ' style="display:none"';
			echo '>';
			echo '<div class="headlinea">';
			if (! $row->image == NULL) {
				if (! $row->shortname == NULL)
					echo '<a href="'.$PHP_SELF.$row->shortname.'">';
				echo '<img src="images/graphics/news/'.$row->image.'"';
				if (! $row->image_caption == NULL)
					echo ' alt="'.$row->image_caption.'" title="'.$row->image_caption.'" ';
				else
					echo ' alt="'.$row->headline.'" title="'.$row->headline.'" ';
				echo ' />';
				if (! $row->shortname == NULL)
					echo '</a>';
				echo '<div>';
			}
			else 
				echo '<div>';
			
			echo '<h1>';
			if (! $row->shortname == NULL)
				echo '<a href="'.$PHP_SELF.$row->shortname.'">'.$row->headline.'</a>';
			else
				echo $row->headline;
			echo '</h1>';
			echo '<p>'.$row->text;
			if (! $row->shortname == NULL)
				echo ' <a href="'.$PHP_SELF.$row->shortname.'">Find out more...</a>';
			echo '</p></div>';
			echo '</div>'; # close "banner_ad"
			echo '<div class="banner_nav">';
			if(mysql_num_rows($result) > 1) {
				$j = 1;
				while ($j <= mysql_num_rows($result)) {
					if($i == $j)
						echo '<img src="images/layout/headline_on.jpg"/>';
					else {
						echo '<script type="text/javascript">document.write("<a href=\'#\' onmouseover=\'headlineOver('.$i.','.$j.');return true;\'><img src=\'images/layout/headline_off.jpg\'/></a>");</script>';
						echo '<noscript><a href="?ad='.$j.'"><img src="images/layout/headline_off.jpg"/></a></noscript>';
					}
					$j++;
				}
			}
			$i++;
			echo '</div>'; # close "banner_nav"
			echo '</div>'; # close containter for ad and nav
		}
		echo '</div>';
		echo '<div class="right"></div>';
		echo '</div>';
	}

 	if(mysql_num_rows($result) > 1) # call jscript function to autoswitch the headlines
		echo '<script type="text/javascript">t = setTimeout("switchHeadlines('.mysql_num_rows($result).',1,2,'.$delay.')",'.$delay.')</script>';

?>
