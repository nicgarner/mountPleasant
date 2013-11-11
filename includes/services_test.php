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
	$query .= "ORDER BY TIME_FORMAT(datetime, '%l%i') ASC, DATE_FORMAT(datetime, '%Y-%m-%d') DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	$lastTime = '0';
	
	echo '<h4>Services this Sunday '.$thisSundayText.':</h4>';
	echo '<table cellpadding="3">';
		while($row = mysql_fetch_object($result)) {
			if ($row->title <> 'CANCELLED') {
				if ($row->time <> $lastTime) {
					echo '<tr><td width="69" align="right"><b>'.strtolower($row->time).'</b></td><td>'.$row->title.'</td></tr>';
					if (! $row->speaker == NULL) {
						echo '<tr><td></td><td>Speaker: '.$row->speaker.'</td>';
					}
					if (! $row->comment == NULL) {
						echo '<tr><td></td><td>'.$row->comment.'</td>';
					}
					$lastTime = $row->time;
				}
				else
					$lastTime = $row->time;
			}
			else {
				$lastTime = $row->time;
			}
		}
	echo '</table>';
	
	$lastTime = '0';
	
	$query  = "SELECT TIME_FORMAT(datetime, '%l.%i %p') as time, title, comment, speaker FROM services ";
	$query .= "WHERE DATE_FORMAT(datetime, '%Y-%m-%d') = '2004-01-$nextWeek' " ;
	$query .= "OR DATE_FORMAT(datetime, '%Y') = '0000' ";
	$query .= "OR DATE_FORMAT(datetime, '%Y-%m-%d') = '$nextSundayDate' ";
	$query .= "ORDER BY TIME_FORMAT(datetime, '%l%i') ASC, DATE_FORMAT(datetime, '%Y-%m-%d') DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	echo '<h4>Services next Sunday '.$nextSundayText.':</h4>';
	echo '<table cellpadding="3">';
		while($row = mysql_fetch_object($result)) {
			if ($row->title <> 'CANCELLED') {
				if ($row->time <> $lastTime) {
					echo '<tr><td width="69" align="right"><b>'.strtolower($row->time).'</b></td><td>'.$row->title.'</td></tr>';
					if (! $row->speaker == NULL) {
						echo '<tr><td></td><td>Speaker: '.$row->speaker.'</td>';
					}
					if (! $row->comment == NULL) {
						echo '<tr><td></td><td>'.$row->comment.'</td>';
					}
					$lastTime = $row->time;
				}
				else
					$lastTime = $row->time;
			}
			else {
				$lastTime = $row->time;
			}
		}
	echo '</table>';
	

?>
