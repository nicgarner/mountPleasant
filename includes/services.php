<?php 

	global $page;

	$today = date("w");
	
	$thisSunday = mktime(0,0,0,date("m"),date("d")+(07-date("N")),date("Y"));
	$nextSunday = mktime(0,0,0,date("m"),date("d")+(14-date("N")),date("Y"));
	
	$thisSundayText = date("jS F",$thisSunday);
	$nextSundayText = date("jS F",$nextSunday);
	
	$thisSundayDay = date("j",$thisSunday);
	$nextSundayDay = date("j",$nextSunday);
	
	$thisSundayDate = date("Y-m-d",$thisSunday);
	$nextSundayDate = date("Y-m-d",$nextSunday);
	
	$thisWeek = ceil($thisSundayDay/7)*7-6;
	$nextWeek = ceil($nextSundayDay/7)*7-6;
	
	if (strlen($thisWeek) == 1)
		$thisWeek = "0".$thisWeek;
	if (strlen($nextWeek) == 1)
		$nextWeek = "0".$nextWeek;
	
	$query  = "SELECT TIME_FORMAT(datetime, '%l.%i %p') as time, title, comment, speaker FROM services ";
	$query .= "WHERE DATE_FORMAT(datetime, '%Y-%m-%d') = '2004-01-$thisWeek' " ;
	$query .= "OR DATE_FORMAT(datetime, '%Y') = '0000' ";
	$query .= "OR DATE_FORMAT(datetime, '%Y-%m-%d') = '$thisSundayDate' ";
	$query .= "ORDER BY TIME_FORMAT(datetime, '%H%i') ASC, DATE_FORMAT(datetime, '%Y-%m-%d') DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	$lastTime = '0';
	
	echo '<div class="ad"><div class="tl"></div><div class="tm"><h4>Upcoming services</h4>';
	echo '<div class="date">Details of our Sunday services over the next two weeks.';
	echo '</div></div><div class="tr"></div><br><div class="mm">';
	echo '<div class="inline250">';
	if (date("w") == 0)
		echo '<b>Today, ';
	else
		echo '<b>This Sunday ';
	echo $thisSundayText.':</b>';
		echo '<table cellpadding="3">';
		while($row = mysql_fetch_object($result)) {
			if ($row->title <> 'CANCELLED') {
				if ($row->time <> $lastTime) {
					echo '<tr valign="top"><td width="69" align="right"><b>'.strtolower($row->time).'</b></td><td>'.$row->title;
					if (! $row->speaker == NULL) {
						echo '<p class="small">Speaker: '.$row->speaker.'</p>';
					}
					if (! $row->comment == NULL) {
						echo '<p class="small">'.$row->comment.'</p>';
					}
					$lastTime = $row->time;
					echo '</td></tr>';
				}
				else
					$lastTime = $row->time;
			}
			else {
				$lastTime = $row->time;
			}
		}
	echo '</table></div>';
	
	$lastTime = '0';
	
	$query  = "SELECT service_id, TIME_FORMAT(datetime, '%l.%i %p') as time, title, comment, speaker FROM services ";
	$query .= "WHERE DATE_FORMAT(datetime, '%Y-%m-%d') = '2004-01-$nextWeek' " ;
	$query .= "OR DATE_FORMAT(datetime, '%Y') = '0000' ";
	$query .= "OR DATE_FORMAT(datetime, '%Y-%m-%d') = '$nextSundayDate' ";
	$query .= "ORDER BY TIME_FORMAT(datetime, '%H%i') ASC, DATE_FORMAT(datetime, '%Y-%m-%d') DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	echo '<div class="inline250">';
	echo '<b>Next Sunday '.$nextSundayText.':</b>';
		echo '<table cellpadding="3">';
		while($row = mysql_fetch_object($result)) {
			if ($row->title <> 'CANCELLED') {
				if ($row->time <> $lastTime) {
					echo '<tr valign="top"><td width="69" align="right"><b>'.strtolower($row->time).'</b></td><td>'.$row->title;
//					echo ' ('.$row->service_id.')';
					if (! $row->speaker == NULL) {
						echo '<p class="small">Speaker: '.$row->speaker.'</p>';
					}
					if (! $row->comment == NULL) {
						echo '<p class="small">'.$row->comment.'</p>';
					}
					$lastTime = $row->time;
					echo '</td></tr>';
				}
				else
					$lastTime = $row->time;
			}
			else {
				$lastTime = $row->time;
			}
		}
	echo '</table></div>';
	echo '</div><div class="bl"></div><div class="bm"></div><div class="br"></div></div>';
	echo '<br class="clear" />';
	

?>
