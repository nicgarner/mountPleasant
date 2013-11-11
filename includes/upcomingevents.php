<?php 

	global $page;
	
	$today = date('Y-m-d');
	
	$query  = "SELECT name, info, location, DATE_FORMAT(date, '%W %D %M') as date, ";
	$query .= "DATE_FORMAT(enddate, '%W %D %M') as enddate, pattern, feature_text FROM events ";
	$query .= "WHERE deleted = 0 AND feature = 1 AND DATE_FORMAT(feature_expire, '%Y-%m-%d') > '$today' " ;
	$query .= "ORDER BY DATE_FORMAT(date, '%Y-%m-%d') ASC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	while($row = mysql_fetch_object($result)) {
		echo '<div class="ad"><div class="tl"></div><div class="tm">';
		echo '<h4>'.$row->name.'</h4>';
		echo '<div class="date">'.($row->date);
		if ($row->enddate <> NULL) {
			if ($row->pattern == 8)
				echo ' to '.$row->enddate;
		}
		echo '</div></div><div class="tr"></div><br><div class="mm"><p>'.$row->feature_text;
		if ($row->info <> NULL)
			echo ' <a href="'.$PHP_SELF.'?'.$row->info.'">Find out more about our '.$row->name.'.</a>';
		echo '</p></div><div class="bl"></div><div class="bm"></div><div class="br"></div>';
		echo '</div>';
		echo '<br class="clear" />';
	}
	
	$query = "UPDATE events SET feature='0' WHERE DATE_FORMAT(feature_expire, '%Y-%m-%d') < '$today' ";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

?>
