<?php 
global $page;
$id = $_GET['aid'];
$display = $_GET['display'];

if ($display == 'month') {
	
	$earliest = mktime(0, 0, 0, date("m")-4, 1, date("Y"));
	$latest = mktime(0, 0, 0, date("m")+8, 1, date("Y"));
	
	$month = $_GET['month'];
	if ($month == NULL) {
		$month = date("m");
		$year = date("Y");
		$timestamp = mktime(0, 0, 0, date("m"), 1, date("Y"));
	}
	
	else {
		switch ($month) {
		case "january":
			$timestamp = mktime(0, 0, 0, 1, 1, date("Y"));
			break;
		case "february":
			$timestamp = mktime(0, 0, 0, 2, 1, date("Y"));
			break;
		case "march":
			$timestamp = mktime(0, 0, 0, 3, 1, date("Y"));
			break;
		case "april":
			$timestamp = mktime(0, 0, 0, 4, 1, date("Y"));
			break;
		case "may":
			$timestamp = mktime(0, 0, 0, 5, 1, date("Y"));
			break;
		case "june":
			$timestamp = mktime(0, 0, 0, 6, 1, date("Y"));
			break;
		case "july":
			$timestamp = mktime(0, 0, 0, 7, 1, date("Y"));
			break;
		case "august":
			$timestamp = mktime(0, 0, 0, 8, 1, date("Y"));
			break;
		case "september":
			$timestamp = mktime(0, 0, 0, 9, 1, date("Y"));
			break;
		case "october":
			$timestamp = mktime(0, 0, 0, 10, 1, date("Y"));
			break;
		case "november":
			$timestamp = mktime(0, 0, 0, 11, 1, date("Y"));
			break;
		case "december":
			$timestamp = mktime(0, 0, 0, 12, 1, date("Y"));
			break;
		default:
			$timestamp = mktime(0, 0, 0, date("m"), 1, date("Y"));
		}
		
		if ($timestamp < $earliest) {
			$timestamp = mktime(0, 0, 0, date("m", $timestamp), 1, date("Y")+1);
		}
		if ($timestamp > $latest) {
			$timestamp = mktime(0, 0, 0, date("m", $timestamp), 1, date("Y")-1);
		}
		$month = date("m", $timestamp);
		$year  = date("Y", $timestamp);
		
		if ((date("m", $latest))-1 == $month) {
			$position = "end";
		}
		if ((date("m", $earliest)) == $month) {
			$position = "start";
		}
	}
		

	$query  = "SELECT name, location, info, TIME_FORMAT(datetime, '%H:%i') as time, DAYOFMONTH(datetime) as day, hidetime ";
	$query .= "FROM events, event_occurrences ";
	$query .= "WHERE events.event_id = event_occurrences.event_id AND event_occurrences.deleted = '0' ";
	$query .= "AND DATE_FORMAT(datetime, '%m %Y') = '$month $year' ";
	$query .= "ORDER BY datetime ASC, name ASC";
	
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	$d = 0;
	$c = 1;
		
	echo '<h3>At Mount Pleasant in ' . date("F Y", $timestamp) . '</h3>';
	
	echo '<div class="calendar">';
	
	echo '<div class="options">';
		echo '<div class="option"><a href="'.$PHP_SELF.'?'.$page.'">Week view</a></div>';
		echo '<div class="selected"><a href="'.$PHP_SELF.'?'.$page.'&display=month">Month view</a></div>';
	echo '</div>';
	
	$lastmonth = mktime(0, 0, 0, date("m", $timestamp)-1, 1, date(Y));
	$daysinlastmonth = date("t", $lastmonth);
	$lastmonth = date("F", $lastmonth);
	
	$nextmonth = mktime(0, 0, 0, date(m, $timestamp)+1, 1, date(Y));
	$nextmonth = date("F", $nextmonth);
	
	echo '<div class="navigation">';
		echo '<div class="left">';
			if ($position == "start")
				echo '<font color="#999999">< '.$lastmonth.'</font>';
			else
				echo '<a href="'.$PHP_SELF.'?'.$page.'&display=month&month='.strtolower($lastmonth).'">< '.$lastmonth.'</a> ';
		echo '</div>';
		echo '<div class="center">';
			if (date("F", $timestamp) <> date("F"))
				echo '<a href="'.$PHP_SELF.'?'.$page.'&display=month&month='.strtolower(date("F")).'">This month</a>';
			else
				echo '<div class="center"><font color="#999999">This month</font></div>';
		echo '</div>';
		echo '<div class="right">';
			if ($position == "end")
				echo '<font color="#999999">'.$nextmonth.' ></font>';
			else
				echo '<a href="'.$PHP_SELF.'?'.$page.'&display=month&month='.strtolower($nextmonth).'">'.$nextmonth.' ></a>';
		echo '</div>';
	echo '</div>';
	echo '<div style="clear:both;"></div>';
	
	
	if (mysql_num_rows($result) > 0) {
	
		echo '<div class="days">Sunday</div><div class="days">Monday</div><div class="days">Tuesday</div><div class="days">Wednesday</div><div class="days">Thursday</div><div class="days">Friday</div><div class="days">Saturday</div>';
		
		while($row = mysql_fetch_object($result)) {
			if ($c == 1) {
				$days = mktime(0, 0, 0, date("m", $timestamp), 1, date("Y", $timestamp));
				$days = date("w", $days);
				
				$i = $daysinlastmonth - $days;
				
				if ($days == 0)
					echo '<div>';
				while ($i < $daysinlastmonth) {
					if ($i <> ($daysinlastmonth - $days)) {
						echo '</div>';
					}
					echo '<div class="grey_day">';
					echo '<h4>'.($i+1);
					if (($i+1) == $daysinlastmonth) {
						echo ' '.$lastmonth;
					}
					echo '</h4>';
					$i++;
					$time = NULL;
				}
			}
			if ($row->day <> $c) {
				$missing = $row->day - $c;
				while ($c <= $row->day) {
					echo '</div>';
					if ($c == date("j") && date("m", $timestamp) == date("m")) {
						echo '<div class="today">';
					}
					else {
						echo '<div class="day">';
					}
					echo '<h4>'.$c;
					if ($c == 1) {
						echo ' '.date("F", $timestamp);
					}
					echo '</h4>';
					$c++;
					$time = NULL;
				}
			}
					
			elseif ($d <> $row->day) {		
				echo '</div>';
				if ($c == date("j") && date("m", $timestamp) == date("m")) {
					echo '<div class="today">';
				}
				else {
					echo '<div class="day">';
				}
				echo '<h4>'.$c;
				if ($c == 1) {
					echo ' '.date("F", $timestamp);
				}
				echo '</h4>';
				$c++;
				$time = NULL;
			}
			
			echo '<p>';
			if ($time <> $row->time) {
				if ($row->time <> '00:00') {
					if ($row->hidetime <> 1) 
						echo '<b>' . $row->time . '</b><br />';
					else {
						if ($time <> 'evening') {
							echo '<b>Evening:</b><br />';
							$time = 'evening';
						}
					}
				}
			}
			if ($row->info <> NULL) {
				echo '<a href="'.$PHP_SELF.'?'.$row->info.'">' . $row->name . '</a>';
			}
			else {
				echo $row->name;
			}
			if ($row->location <> NULL) {
				echo ' ('.$row->location.')';
			}
			echo '</p>';
			if (!time == 'evening') {
				$time = $row->time;
			}
			$d = $row->day;
		}
		
		$missing = date("t", $timestamp);
		while ($c <= $missing) {
			echo '</div><div class="day">';
			echo '<h4>'.$c.'</h4>';
			$c++;
		}
		
		$day = mktime(0, 0, 0, date("m", $timestamp), date("t", $timestamp), date("Y", $timestamp));
		$day = date("w", $day);
		if ($day == 0)
			$day = 0;
		$i = 1;
		while ($day < 6) {
			echo '</div><div class="grey_day">';
			echo '<h4>'.$i;
			if ($i == 1) {
				echo ' '.$nextmonth;
			}
			echo '</h4>';
			$i++;
			$day++;
		}
		
		echo '</div>';

		
	}
	else {
		echo 'There are no events to display yet in ' . date("F", $timestamp) . '. Come back soon!<br/><br/>';
	}
	echo '</div>';
}


else {
	$week = $_GET['week'];
	if ($week == 'next') {
		$timestamp = mktime(0, 0, 0, date("m"), date("d")+7, date("Y"));
		$name = "next week";
	}
	elseif ($week == 'last') {
		$timestamp = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
		$name = "last week";
	}
	else {
		$timestamp = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$name = "this week";
	}
	
	if (date("l", $timestamp) == 'Sunday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-0, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+7, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+6, date(Y, $timestamp));
	}
	if (date("l", $timestamp) == 'Monday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-1, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+6, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+5, date(Y, $timestamp));
	}
	if (date("l", $timestamp) == 'Tuesday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-2, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+5, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+4, date(Y, $timestamp));
	}
	if (date("l", $timestamp) == 'Wednesday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-3, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+4, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+3, date(Y, $timestamp));
	}
	if (date("l", $timestamp) == 'Thursday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-4, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+3, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+2, date(Y, $timestamp));
	}
	if (date("l", $timestamp) == 'Friday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-5, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+2, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+1, date(Y, $timestamp));
	}
	if (date("l", $timestamp) == 'Saturday') {
		$sun1 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)-6, date(Y, $timestamp));
		$sun2 = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+1, date(Y, $timestamp));
		$sat  = mktime(0, 0, 0, date(m, $timestamp), date(d, $timestamp)+0, date(Y, $timestamp));
	}
	
	//this is to ensure events that start at 00:00 on the next Sunday aren't included in the SQL select query 
	$sun2 = $sun2-1;
	
	echo "<h3>At Mount Pleasant $name</h3>";
	
	echo '<div class="calendar">';
	
	echo '<div class="options">';
		echo '<div class="selected"><a href="'.$PHP_SELF.'?'.$page.'">Week view</a></div>';
		echo '<div class="option"><a href="'.$PHP_SELF.'?'.$page.'&display=month">Month view</a></div>';
	echo '</div>';
	
	echo '<div class="navigation">';
		echo '<div class="left">';
			if ($week == "last")
				echo '<font color="#999999">Last week</font>';
			else
				echo '<a href="'.$PHP_SELF.'?'.$page.'&display=week&week=last">Last week</a> ';
		echo '</div>';
		echo '<div class="center">';
			if ($week == NULL)
				echo '<font color="#999999">This week</font>';
			else
				echo '<a href="'.$PHP_SELF.'?'.$page.'&display=week">This week</a>';
		echo '</div>';
		echo '<div class="right">';
			if ($week == "next")
				echo '<font color="#999999">Next week</font>';
			else
				echo '<a href="'.$PHP_SELF.'?'.$page.'&display=week&week=next">Next week</a>';
		echo '</div>';
	echo '</div>';
	echo '<div style="clear:both;"></div>';
	echo '</div>';
	
	echo '<p>Details of what ';
	if ($name == 'last week')
		echo 'happened ';
	else
		echo 'is happening ';
	echo $name.': <b>' . date("j F", $sun1) . ' - ' . date("j F", $sat) . ' ' . date(Y) . '</b>.';
	
	$query  = "SELECT name, location, info, TIME_FORMAT(datetime, '%H:%i') as time, DAYOFWEEK(datetime) as day, hidetime ";
	$query .= "FROM events, event_occurrences ";
	$query .= "WHERE events.event_id = event_occurrences.event_id AND event_occurrences.deleted = '0' ";
	$query .= "AND (UNIX_TIMESTAMP(datetime) BETWEEN '$sun1' AND '$sun2') ";
	$query .= "ORDER BY datetime ASC, name ASC";
	
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	$d = -1;
	
	if (mysql_num_rows($result) > 0) {
		
		while($row = mysql_fetch_object($result)) {		
			$day = $row->day - 1;
			if ($day <> $d) {
				if ($day == 0) {
					if (date("l") == 'Sunday' && $week == NULL) {
						echo '<div class="diary_today">';
					}
					else {
						echo '<div class="diary">';
					}
					echo '<b>Sunday '.date("j F", $sun1).'</b>';
				}
				elseif ($day == 1) {
					if (date("l") == 'Monday' && $week == NULL) {
						echo '</div><div class="diary_today">';
					}
					else {
						echo '</div><div class="diary">';
					}
					echo '<b>Monday '.date("j F", mktime(0, 0, 0, date("m", $sun1), date("j", $sun1)+1, date("Y", $sun1))).'</b>';
				}
				if ($day == 2) {
					if (date("l") == 'Tuesday' && $week == NULL) {
						echo '</div><div class="diary_today">';
					}
					else {
						echo '</div><div class="diary">';
					}
					echo '<b>Tuesday '.date("j F", mktime(0, 0, 0, date("m", $sun1), date("j", $sun1)+2, date("Y", $sun1))).'</b>';
				}
				if ($day == 3) {
					if (date("l") == 'Wednesday' && $week == NULL) {
						echo '</div><div class="diary_today">';
					}
					else {
						echo '</div><div class="diary">';
					}
					echo '<b>Wednesday '.date("j F", mktime(0, 0, 0, date("m", $sun1), date("j", $sun1)+3, date("Y", $sun1))).'</b>';
				}
				if ($day == 4) {
					if (date("l") == 'Thursday' && $week == NULL) {
						echo '</div><div class="diary_today">';
					}
					else {
						echo '</div><div class="diary">';
					}
					echo '<b>Thursday '.date("j F", mktime(0, 0, 0, date("m", $sun1), date("j", $sun1)+4, date("Y", $sun1))).'</b>';
				}
				if ($day == 5) {
					if (date("l") == 'Friday' && $week == NULL) {
						echo '</div><div class="diary_today">';
					}
					else {
						echo '</div><div class="diary">';
					}
					echo '<b>Friday '.date("j F", mktime(0, 0, 0, date("m", $sun1), date("j", $sun1)+5, date("Y", $sun1))).'</b>';
				}
				if ($day == 6) {
					if (date("l") == 'Saturday' && $week == NULL) {
						echo '</div><div class="diary_today">';
					}
					else {
						echo '</div><div class="diary">';
					}
					echo '<b>Saturday '.date("j F", mktime(0, 0, 0, date("m", $sun1), date("j", $sun1)+6, date("Y", $sun1))).'</b>';
				}
			}
				echo '<div class="diaryentry"><div class="time">';
				if ($row->hidetime == 0) {
					if ($row->time == '00:00')
						echo 'All day';
					else
						echo $row->time;
				}
				else {
					echo 'Evening:';
				}
				echo '</div><div class="activity">';
				if ($row->info <> NULL) {
					echo '<a href="'.$PHP_SELF.'?'.$row->info.'">' . $row->name . '</a>';
				}
				else {
					echo $row->name;
				}
				if ($row->location <> NULL) {
					echo ' (' . $row->location . ')';
				}
				echo '</div></div>';
	
			$d = $day;
		}
	}


	echo '</div>';

}

?>
