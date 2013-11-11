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
	
echo '<div class="comingup">';
	
	echo '<h4>';
	if (date("w") == 0)
		echo 'Today, ';
	else
		echo 'This Sunday ';
	echo $thisSundayText.'</h4>';
		$topline = 0;
		while($row = mysql_fetch_object($result)) {
			if ($row->title <> 'CANCELLED') {
				if ($row->time <> $lastTime) {
					echo '<p class="small';
					if (!$topline == 0)
						echo ' topline';
					echo '">';
					if (! $row->comment == NULL)
						echo '<strong>'.$row->comment.'</strong>';
					else
						echo '<strong>'.$row->title.'</strong>';
					if (! $row->speaker == NULL) {
#						if (! $row->comment == NULL)
#							echo ' by '.$row->speaker;
#						else
							echo ' '.$row->speaker;
					}
#					if ($row->comment == NULL && $row->speaker == NULL)
#						echo '<strong>'.strtolower($row->time).' '.$row->title.'</strong>';
#					else
						echo '<br /><span class="light">'.strtolower($row->time);
						if (! $row->comment == NULL)
							echo ' '.$row->title;
						echo '</span>';
					echo '</p>';
					$lastTime = $row->time;
					$topline++;
				}
				else
					$lastTime = $row->time;
			}
			else {
				$lastTime = $row->time;
			}
		}
	
	$query  = "SELECT name, info, location, DATE_FORMAT(date, '%W') as day, TIME_FORMAT(date, '%l.%i %p') as time, ";
	$query .= "DATE_FORMAT(enddate, '%W') as endday, DATE_FORMAT(enddate, '%W %D %M') as enddate, pattern FROM events ";
	$query .= "WHERE deleted = 0 AND important = 1 " ;
	$query .= "ORDER BY DATE_FORMAT(date, '%Y-%m-%d') ASC, TIME_FORMAT(date, '%H%i') ASC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	if (mysql_num_rows($result) > 0) {
		echo '<h4>This week</h4>';
			$topline = 0;
			while($row = mysql_fetch_object($result)) {
				echo '<p class="small';
				if (!$topline == 0)
					echo ' topline';
				echo '">';
				echo '<strong>';
				if ($row->info <> NULL)
					echo '<a href="'.$row->info.'">'.$row->name.'</a>';
				else
					echo $row->name;
				echo '</strong><br/>';
				echo '<span class="light">'.$row->day.' '.strtolower($row->time);
#				if ($row->enddate <> NULL) {
#					if ($row->pattern == 8)
#						echo ' to '.$row->enddate;
#				}
				if ($row->location <> NULL) echo ', '.$row->location;
				$topline++;
			}	
	}
	
	$query = "UPDATE events SET feature='0' WHERE DATE_FORMAT(feature_expire, '%Y-%m-%d') < '$today' ";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	
	$lastTime = '0';
	
	$query  = "SELECT service_id, TIME_FORMAT(datetime, '%l.%i %p') as time, title, comment, speaker FROM services ";
	$query .= "WHERE DATE_FORMAT(datetime, '%Y-%m-%d') = '2004-01-$nextWeek' " ;
	$query .= "OR DATE_FORMAT(datetime, '%Y') = '0000' ";
	$query .= "OR DATE_FORMAT(datetime, '%Y-%m-%d') = '$nextSundayDate' ";
	$query .= "ORDER BY TIME_FORMAT(datetime, '%H%i') ASC, DATE_FORMAT(datetime, '%Y-%m-%d') DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	echo '<h4>Next Sunday '.$nextSundayText.'</h4>';
		$topline = 0;
		while($row = mysql_fetch_object($result)) {
			if ($row->title <> 'CANCELLED') {
				if ($row->time <> $lastTime) {
					echo '<p class="small';
					if (!$topline == 0)
						echo ' topline';
					echo '">';
					if (! $row->comment == NULL)
						echo '<strong>'.$row->comment.'</strong>';
					else
						echo '<strong>'.$row->title.'</strong>';
					if (! $row->speaker == NULL) {
#						if (! $row->comment == NULL)
#							echo ' by '.$row->speaker;
#						else
							echo ' '.$row->speaker;
					}
#					if ($row->comment == NULL && $row->speaker == NULL)
#						echo '<strong>'.strtolower($row->time).' '.$row->title.'</strong>';
#					else
						echo '<br /><span class="light">'.strtolower($row->time);
						if (! $row->comment == NULL)
							echo ' '.$row->title;
						echo '</span>';
					echo '</p>';
					$lastTime = $row->time;
					$topline++;
				}
				else
					$lastTime = $row->time;
			}
			else {
				$lastTime = $row->time;
			}
		}
	echo '<br class="clear" />';
	
echo '</div>';

?>
