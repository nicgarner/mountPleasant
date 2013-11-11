<?php
	
	$oid  = $_GET['oid'];
	$eid  = $_GET['eid'];
	$mode = $_GET['action'];
	
	include '../includes/connect.php'; 
	include '../includes/functions.php'; 
	
	if (isset($_COOKIE["cookie:mpbcadmin"])) {
		$uid = $_COOKIE['cookie:mpbcadmin'];
		$query = "SELECT name FROM users WHERE user_id = $uid";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		while ($row = mysql_fetch_assoc($result)) {
			$username = $row['name'];
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Mount Pleasant Baptist Church, Northampton</title>
            <meta name="Author" content="Mount Pleasant Baptist Church" />
            <meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, Jesus, Holy, Spirit, Kettering Road, Jeff, Taylor, Paul, Lavender, service, services, Mount, Pleasant, town, centre" />
            <meta name="Description" content="Mount Pleasant Baptist Church, 147 Kettering Road, Northampton" />
            <link rel="shortcut icon" href="../admin/images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="../admin/images/favicon.ico" type="image/ico" />
            <link rel="stylesheet" media="all" type="text/css" href="../site.css" />

		</head>

		<body>
        
        <div class="site">
            
            <a href="/"><img src="../images/layout/banner.jpg" border="0" /></a>
            
            <div class="menu"><ul><li><a href="/">Home</a></li><li><a href="/login">Admin Home</a></li></ul></div>
            
            
                
                <div class="page">
                
<?php
	// find out if the user has a cookie and show the page if so
	if (isset($_COOKIE["cookie:mpbcadmin"])) {       
	
		// if not editing a paticular event, render the calender for the current mornth
		if ($oid == NULL && $eid == NULL) {
			
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
			echo '<h3>Edit calendar for '. date("F Y", $timestamp) .'</h3>'; 
			
			//select the event_occurences for this month and get the date from the events table that match them
			$query  = "SELECT events.event_id, event_occurrence_id, name, location, reoccuring, ";
			$query .= "TIME_FORMAT(datetime, '%H:%i') as time, DAYOFMONTH(datetime) as day ";
			$query .= "FROM events, event_occurrences ";
			$query .= "WHERE events.event_id = event_occurrences.event_id AND event_occurrences.deleted = '0' ";
			$query .= "AND DATE_FORMAT(datetime, '%m %Y') = '$month $year' ";
			$query .= "ORDER BY datetime ASC, name ASC";
			
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			
			echo '<div class="calendar">';
				
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
							echo '<a href="'.$PHP_SELF.'?month='.strtolower($lastmonth).'">< '.$lastmonth.'</a> ';
					echo '</div>';
					echo '<div class="center">';
						if (date("F", $timestamp) <> date("F"))
							echo '<a href="'.$PHP_SELF.'?month='.strtolower(date("F")).'">This month</a>';
						else
							echo '<div class="center"><font color="#999999">This month</font></div>';
					echo '</div>';
					echo '<div class="right">';
						if ($position == "end")
							echo '<font color="#999999">'.$nextmonth.' ></font>';
						else
							echo '<a href="'.$PHP_SELF.'?month='.strtolower($nextmonth).'">'.$nextmonth.' ></a>';
					echo '</div>';
				echo '</div>';
				echo '<div style="clear:both;"></div>';
				
			echo '<p>Click an event name to edit the details. Click the \'x\' after the name to delete the event. <a href="'.$PHP_SELF.'?eid=0&action=edit">Create a new event.</a></p>';
			
			//set the 'day' to 0 and the 'count' to 1. these are used when drawing the calender to print empty days and grey days
			//that are not in this month
			$d = 0;
			$c = 1;
			
			if (mysql_num_rows($result) > 0) {
				
				//print the column headings	for each day
				echo '<div class="days">Sunday</div><div class="days">Monday</div><div class="days">Tuesday</div><div class="days">Wednesday</div><div class="days">Thursday</div><div class="days">Friday</div><div class="days">Saturday</div>';
				
				while($row = mysql_fetch_object($result)) {
					//if we haven't printed any days yet
					if ($c == 1) {
						
						//find out what day of the week the last month ended on
						$days = mktime(0, 0, 0, date("m", $timestamp), date(1), date("Y", $timestamp));
						$days = date("w", $days);
						
						if ($days == 0)
							echo '<div class="calendar">';
						
						//work out how many days to draw to reprensent the previous month
						$i = $daysinlastmonth - $days;
						
						//draw the days for the previous month
						while ($i < $daysinlastmonth) {
							if ($i <> ($daysinlastmonth - $days)) {
								echo '</div>';
							}
							echo '<div class="grey_day">';
							echo '<h4>'.($i+1);
							// print the month name for the last day
							if (($i+1) == $daysinlastmonth) {
								echo ' '.$lastmonth;
							}
							echo '</h4>';
							$i++;
							$time = NULL;
						}
					}
					//if the date of the next event to print isn't on the day we've drawn up to so far, draw some empty days
					if ($row->day <> $c) {
						//work out how many empty days to draw
						$missing = $row->day - $c;
						while ($c <= $row->day) {
							echo '</div>';
							//colour the day green if its today
							if ($c == date("j") && date("m", $timestamp) == date("m")) {
								echo '<div class="today">';
							}
							else {
								echo '<div class="day">';
							}
							//print the day's number
							echo '<h4>'.$c;
							//print this month's name if its the first day of this month
							if ($c == 1) {
								echo ' '.date("F", $timestamp);
							}
							echo '</h4>';
							$c++;
							//set the $time to NULL because we want to print the time of the first event on the next day regardless of
							//whether it is at the same time as the last event today
							$time = NULL;
						}
					}
					//if this event is not on the same day as the previous event, start printing a new day
					elseif ($d <> $row->day) {		
						echo '</div>';
						//colour the day green if its today
						if ($c == date("j") && date("m", $timestamp) == date("m")) {
							echo '<div class="today">';
						}
						else {
							echo '<div class="day">';
						}
						//print the day's number
						echo '<h4>'.$c;
						//print this month's name if its the first day of this month
						if ($c == 1) {
							echo ' '.date("F", $timestamp);
						}
						echo '</h4>';
						$c++;
						//set the $time to NULL because we want to print the time of the first event on the next day regardless of
						//whether it is at the same time as the last event today
						$time = NULL;
					}
					//print the event details, each as a new paragraph
					echo '<p>';
					//if the time of this event is different to the time of the previous event (stored in $time) and...
					if ($time <> $row->time) {
						//... its not all day event, print the time of the event
						if ($row->time <> '00:00') {
							echo '<b>' . $row->time . '</b><br />';
						}
					}
					//print a link to edit the event details and a link to delete the event
					//if not a reoccuring event, link straight to the main event, not the occurrence
					if ($row->reoccuring == 0)
						echo '<a href="'.$PHP_SELF.'?eid='.$row->event_id;
					else
						echo '<a href="'.$PHP_SELF.'?oid='.$row->event_occurrence_id;
					echo '&action=edit" title="edit">' . $row->name . '</a> ';
					if ($row->reoccuring == 0)
						echo '<a href="'.$PHP_SELF.'?eid='.$row->event_id;
					else
						echo '<a href="'.$PHP_SELF.'?oid='.$row->event_occurrence_id;
					echo '&action=delete" title="delete">x</a>';
					if ($row->location <> NULL) {
						echo ' ('.$row->location.')';
					}
					echo '</p>';
					//store the time so that we don't print the time again if the next event is at the same time
					$time = $row->time;
					//store the day of this event so that we can decide whether to start drawing the next day for the next event
					$d = $row->day;
				}
				//after printing all of this mornths events, work out how many days there are in this month and then...
				$missing = date("t", $timestamp);
				//...draw empty days until the end of this month to complete the month
				while ($c <= $missing) {
					echo '</div><div class="day">';
					echo '<h4>'.$c.'</h4>';
					$c++;
				}
				//work out what day the next month starts on
				
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
					$time = NULL;
				}
				
				echo '</div></div>';
			}
			else {
				echo 'There are no events to display yet in ' . date("F", $timestamp) . '.<br/><br/>';
			}
		}
		else {
			if ($_POST['submit']) {
				if ($oid == NULL && $eid == 0)
					echo '<div class="page">Creating event...</div>';
				elseif ($mode == 'edit')
					echo '<div class="page">Updating event...</div>';
				elseif ($mode == 'delete')
					echo '<div class="page">Deleting event...</div>';
				

				$name          = $_POST['name'];
				$location      = $_POST['location'];
				$info          = $_POST['link'];
				$hours         = $_POST['hours'];
				$minutes       = $_POST['minutes'];
				$recurrence    = $_POST['recurrence'];
				$feature	   = $_POST['feature'];
					if ($feature == 'on')
						$feature = 1;
					else
						$feature = 0;
				$featureText   = $_POST['featureText'];
				$featureExpire = $_POST['expireYear'].'-'.$_POST['expireMonth'].'-'.$_POST['expireDay'];
				
				if ($info == '0') {
					$info = NULL;
				}
				
				if ($oid == NULL && $eid == 0) {
					if ($recurrence == 0) {
						$day   = $_POST['day'];
						$month = $_POST['month'];
						$year  = $_POST['year'];
						$date  = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':00';
						$frequency  = NULL;
						$pattern    = NULL;
						$enddate    = NULL;
					}
					if ($recurrence == 1) {
						$frequency  = $_POST['1frequency'];
						$pattern    = $_POST['1pattern'];
						$startday   = $_POST['1startday'];
						$startmonth = $_POST['1startmonth'];
						$startyear  = $_POST['1startyear'];
						$endday     = $_POST['1endday'];
						$endmonth   = $_POST['1endmonth'];
						$endyear    = $_POST['1endyear'];
						$date  = $startyear.'-'.$startmonth.'-'.$startday.' '.$hours.':'.$minutes.':00';
						$enddate = $endyear.'-'.$endmonth.'-'.$endday.' 00:00:00';
					}
					if ($recurrence == 2) {
						$frequency  = $_POST['2frequency'];
						$pattern    = $_POST['2pattern'];
						$startday   = $_POST['2startday'];
						$startmonth = $_POST['2startmonth'];
						$startyear  = $_POST['2startyear'];
						$endday     = $_POST['2endday'];
						$endmonth   = $_POST['2endmonth'];
						$endyear    = $_POST['2endyear'];
						$date  = $startyear.'-'.$startmonth.'-'.$startday.' '.$hours.':'.$minutes.':00';
						$enddate = $endyear.'-'.$endmonth.'-'.$endday.' 00:00:00';
					}
				}
				else {
					$day   = $_POST['day'];
					$month = $_POST['month'];
					$year  = $_POST['year'];
					$datetime  = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':00';
					if ($oid == NULL) {
						$enddate = $_POST['endyear'].'-'.$_POST['endmonth'].'-'.$_POST['endday'].' 00:00:00';
						$oldhours = $_POST['oldhours'];
						$oldminutes = $_POST['oldminutes'];
						$frequency  = $_POST['frequency'];
						$pattern    = $_POST['pattern'];
					}
				}
				
				if ($oid == NULL && $eid == 0) {		
					$query  =  "INSERT INTO events(name, location, info, reoccuring, frequency, pattern, date, enddate, feature, feature_text, feature_expire) ";
					$query .= "VALUES('$name', '$location', '$info', '$recurrence', '$frequency', '$pattern', '$date', '$enddate', '$feature', '$featureText', '$featureExpire')";	
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
					$event_id = mysql_insert_id();
				
					if ($recurrence == 0) {
						$occurences = 1;
						$query  = "INSERT INTO event_occurrences(event_id, datetime) VALUES('$event_id', '$date')";
						$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					}
				
				
					elseif ($recurrence == 1) {
						if ($pattern == 8) {
							$occurence = $frequency * 1;
						}
						if ($pattern == 9) {
							$occurence = $frequency * 7;
						}
						if ($pattern == 11) {
							//this should work differently... same date each year, because of leap years
							$occurence = $frequency * 365;
						}
						
						$date    = strtotime($date);
						$enddate = strtotime($enddate);
						
						$datetime = date("Y", $date).'-'.date("m", $date).'-'.date("d", $date).' '.date("H", $date).':'.date("i", $date).':00';
						
						$query  = "INSERT INTO event_occurrences(event_id, datetime) VALUES('$event_id', '$datetime')";
						$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
						
						$i = 1;
	
						while ($date <= $enddate) {
							$date = mktime(date("H", $date), date("i", $date), 0, date("m", $date), date("d", $date)+$occurence, date("Y", $date));
							$datetime = date("Y", $date).'-'.date("m", $date).'-'.date("d", $date).' '.date("H", $date).':'.date("i", $date).':00';
							$query  = "INSERT INTO event_occurrences(event_id, datetime) VALUES('$event_id', '$datetime')";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
							$i++;
						}
						
						echo '<br /><br />' . $i . ' events were created.';
						
					}
				}
				
				else {
					if ($mode <> 'delete') {
						if ($oid <> NULL) {
							$query = "UPDATE event_occurrences SET datetime='$datetime' WHERE event_occurrence_id='$oid'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
							$month = strtolower(date("F", $datetime));
							echo $month;
						}
						else {
							//update the details in the event table
							$query  = "UPDATE events SET name='$name', info='$info', location='$location', date='$datetime', feature='$feature', feature_text='$featureText', feature_expire='$featureExpire' ";
							$query .= "WHERE event_id='$eid'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
							
							//if not a reoccuring event and the time has changed, update the matching row in occurrences table
							if ($recurrence == 0) {
								if ($hours <> $oldhours || $minutes <> $oldminutes) {
									$query = "UPDATE event_occurrences SET datetime='$datetime' WHERE event_id='$eid'";
									$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
								}
							}
							
							//for reoccuring events:
							else {
								
								//if the time has changed, update all the relavant rows in the occurrances table
								if ($hours <> $oldhours || $minutes <> $oldminutes) {
									if ($pattern == 8) {
										$occurence = $frequency * 1;
									}
									if ($pattern == 9) {
										$occurence = $frequency * 7;
									}
									if ($pattern == 11) {
										//this should work differently... same date each year, because of leap years
										$occurence = $frequency * 365;
									}
									
									$datetime = strtotime($datetime);
									$enddate  = strtotime($enddate);
									
									$enddate = mktime(date("H", $enddate), date("i", $enddate), 0, date("m", $enddate), date("d", $enddate)+$occurence, date("Y", $enddate));
	
									while ($datetime <= $enddate) {
										$olddatetime = date("Y", $datetime).'-'.date("m", $datetime).'-'.date("d", $datetime).' '.$oldhours.':'.$oldminutes.':00';
										$newdatetime = date("Y", $datetime).'-'.date("m", $datetime).'-'.date("d", $datetime).' '.date("H", $datetime).':'.date("i", $datetime).':00';
										$datetime = mktime(date("H", $datetime), date("i", $datetime), 0, date("m", $datetime), date("d", $datetime)+$occurence, date("Y", $datetime));
										$query  = "UPDATE event_occurrences SET datetime='$newdatetime' WHERE datetime='$olddatetime'";
										$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
									}
								}
							}
						}
					}
					else {
						//delete one occurrence of a reoccuring event
						if ($oid <> NULL) {
							$query  = "UPDATE event_occurrences SET deleted = '1' WHERE event_occurrence_id = '$oid'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
						}
						//delete all occurrences of an event, whether reoccuring or not
						else {
							$query  = "UPDATE event_occurrences SET deleted = '1' WHERE event_id = '$eid'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
							$query  = "UPDATE events SET deleted = '1' WHERE event_id = '$eid'";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
						}
						$month = $_GET['return'];
					}
				}
				
				echo '<meta http-equiv="refresh" content="0;url='. $_SERVER['PHP_SELF'] .'?month='.strtolower(date("F", mktime(0,0,0,$month,1,$year))).'">';
			}
			else {
				echo '<h3>Calendar: ';
				if ($mode == 'delete')
					echo 'Delete event';
				elseif ($mode == 'edit') {
					if ($eid == 0 && $oid == 0) {
						echo 'Add event';
					}
					else {
						echo 'Edit event';
					}
				}
				// <img src="../images/icons/help.png" alt="help icon" title="help" border="0" onClick='document.getElementById("addeventpopup").style.display="block"'/>
				echo '</h3>';	
				
				
				if ($eid <> 0 || $oid <> 0) {
					if ($eid == 0) {
						$query  = "SELECT events.event_id, name, location, reoccuring, info, datetime, feature, feature_text, feature_expire ";
						$query .= "FROM events, event_occurrences ";
						$query .= "WHERE event_occurrences.event_id = events.event_id AND event_occurrence_id = '$oid'";
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					}
					else {
						$query  = "SELECT event_id, name, location, reoccuring, frequency, pattern, info, date, enddate, feature, feature_text, feature_expire  ";
						$query .= "FROM events ";
						$query .= "WHERE event_id = '$eid'";
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					}
					
					if (mysql_num_rows($result) > 0) {
						while($row = mysql_fetch_object($result)) {
							$event_id = $row->event_id;
							$name = $row->name;
							$location = $row->location;
							$info = $row->info;
							$feature = $row->feature;
								$featureText = $row->feature_text;
								$expireTime = strtotime($row->feature_expire);
								if ($expireTime == NULL) {
									$expireDay   = 0;
									$expireMonth = 0;
									$expireYear  = 0;
								}
								else {
									$expireDay   = date("d", $expireTime); 
									$expireMonth = date("m", $expireTime);
									$expireYear  = date("Y", $expireTime);
								}
							$reoccuring = $row->reoccuring;
							$frequency = $row->frequency;
							$pattern = $row->pattern;
							if ($eid == 0) {
								$datetime = strtotime($row->datetime);
								$eventhours   = date("H", $datetime);
								$eventminutes = date("i", $datetime);
								$eventday     = date("d", $datetime);
								$eventmonth   = date("m", $datetime);
								$eventyear    = date("Y", $datetime);
							}
							else {
								$date = strtotime($row->date);
								$eventhours   = date("H", $date);
								$eventminutes = date("i", $date);
								$eventday     = date("d", $date);
								$eventmonth   = date("m", $date);
								$eventyear    = date("Y", $date);
							}
							if ($reoccuring == 1) {
								$enddate  = strtotime($row->enddate);
								$endday   = date("d", $enddate);
								$endmonth = date("m", $enddate);
								$endyear  = date("Y", $enddate);
							}						
						}
					}
					else {
						echo 'This event doesn\'t exist.';
					}
				}
	
				

				if ($oid <> 0) {
					if ($mode == 'edit') {
						echo '<div class="warning"><p>You are editing <b>one occurence</b> of a reoccuring event, so you can ';
						echo 'only change the date and time of this occurence. If you would like to edit <b>all occurences</b> ';
						echo 'of this event, <a href="'.$PHP_SELF.'?eid='.$event_id.'&action=edit">click here</a>.</p></div>';
					}
					elseif ($mode == 'delete') {
						echo '<div class="warning"><p>You are deleting <b>one occurence</b> of a reoccuring event If you would like ';
						echo 'to delete <b>all occurences</b> of this event, ';
						echo '<a href="'.$PHP_SELF.'?eid='.$event_id.'&action=delete">click here</a>.</p></div>';
					}
				}
				elseif ($eid <> 0 && $reoccuring <> 0) {
					if ($mode == 'edit') {
						echo '<div class="warning"><p>You are editing a <b>reoccuring event</b>. Changes you make here will affect ';
						echo 'all occurences of this event. If you would like to edit a single occurence, select the event you want ';
						echo 'to edit on the <a href="'.$_SERVER['PHP_SELF'].'">calendar</a>.</p></div>';
					}
					elseif ($mode == 'delete') {
						echo '<div class="warning"><p>You are deleting a <b>reoccuring event</b>. All occurences of this event will ';
						echo 'be deleted if you proceed. If you would like to delete a single occurence, select \'x\' next to the ';
						echo 'event you want to delete on the <a href="'.$_SERVER['PHP_SELF'].'">calendar</a>.</p></div>';
					}
				}
				
				echo '<form name="editevent"  method="post" action="'. $_SERVER['PHP_SELF'] .'?';
				if ($oid == NULL && $eid == NULL)
					echo "eid=0";
				elseif ($oid <> NULL)
					echo "oid=".$oid;
				else
					echo "eid=".$eid;
				if ($mode == 'delete')
					echo "&action=delete&return=".strtolower(date('m', $datetime));
				elseif ($mode == 'edit')
					echo "&action=edit";
				echo '" />';
				
				if ($mode == 'edit') {
					echo '<h4>Event Details:</h4>';
					
					echo '<div class="label">Event name:</div>';
					echo '<input name="name" maxlength="120" size="50" value="'. $name .'"';
					if ($oid <> 0)
						echo 'disabled="disabled"';
					echo ' />';
					echo '<br />';
					
					echo '<div class="label">Location:</div>';
					echo '<input name="location" maxlength="50" size="50" value="'. $location .'"';
					if ($oid <> 0)
						echo 'disabled="disabled"';
					echo ' />';
					echo '<br />';
					
					echo '<div class="label">Link:</div>';
					$query = "SELECT name, shortname FROM content ORDER BY name ASC";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
							
					// check if records were returned
					if (mysql_num_rows($result) > 0) {	
						echo '<select name="link"';
						if ($oid <> 0)
							echo 'disabled="disabled"';
						echo ' /><option value="0">None</option>';
						while($row = mysql_fetch_object($result)) {
							echo '<option value="' . $row->shortname . '"';
								if ($info == $row->shortname) {
									echo ' SELECTED';
								}
							echo '>' . $row->name . '</option>';
						}
						echo '</select>';
					}
					echo '<br />';
					echo '<div class="label">Feature on Home?</div><input type="checkbox" name="feature"';
						if ($feature == 1) { echo 'checked="checked"'; }
						if ($oid <> 0) { echo 'disabled="disabled"'; }
					echo '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expires: ';

					echo '<select name="expireDay"';
						if ($oid <> 0) { echo ' disabled="disabled"'; }
					echo '>';
					$i = 0;
					while($i <= 31) {
						echo '<option value="'.$i.'"';
						if ($eid == 0 && $oid == NULL) {
							if ($i == 0)
								echo ' selected="SELECTED"';
						}
						elseif ($i == $expireDay)
							echo ' selected="SELECTED"';
						echo '>';
						if ($i == 0)
							echo '--';
						else	
							echo $i;
						echo '</option>';
						$i++;
					}
					echo '</select>';

					echo '<select name="expireMonth"';
						if ($oid <> 0) { echo ' disabled="disabled"'; }
					echo '>';
					$i = 0;
					while($i <= 12) {
						echo '<option value="';
						if ($i == 0)
							echo '0';
						else
							echo date("m", strtotime('2004-'.$i.-'1'));
						echo '"';
						if ($eid == 0 && $oid == NULL) {
							if ($i == 0)
								echo ' selected="SELECTED"';
						}
						elseif ($i == $expireMonth)
							echo ' selected="SELECTED"';
						echo '>';
						if ($i == 0)
							echo '--';
						else
							echo date("F", strtotime('2004-'.$i.-'1'));
						echo '</option>';
						$i++;
					}
					echo '</select>';
					
					echo '<select name="expireYear"';
						if ($oid <> 0) { echo ' disabled="disabled"'; }
					echo '>';
					$i = date("Y");
					$j = $i+1;
					
					echo '<option value="0"';
					if ($eid == 0 && $oid == NULL)
						echo ' selected="SELECTED"';
					if ($expireYear == 0)
						echo ' selected="SELECTED"';
					echo '>--</option>';
					while($i <= $j) {
						echo '<option value="'.$i.'"';
						if ($i == $expireYear) {
							echo ' selected="SELECTED"';
						}
						echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
						$i++;
					}		
					echo '</select>';
					
					echo '<br/>';
					echo '<div class="label">Feature text:</div> <textarea name="featureText" cols="70" rows="2"';
						if ($oid <> 0) { echo ' disabled="disabled"'; }
					echo '>'.$featureText.'</textarea><br/>';
					echo '<div class="label">Start time:</div>';
					if ($eid == 0 && $oid == NULL) {
						echo '<input name="hours" maxlength="2" size="1" value="'. $hours .'" />:';
						echo '<input name="minutes" maxlength="2" size="1" value="'. $minutes .'" /> ';
						echo '<font size="1">(Enter 00:00 for an all day event.)</font>';
					}
					else {
						echo '<input name="hours" maxlength="2" size="1" value="'. $eventhours .'" />:';
						echo '<input name="minutes" maxlength="2" size="1" value="'. $eventminutes .'" /> ';
						echo '<font size="1">(Enter 00:00 for an all day event.)</font><br />';
						if ($eid == 0) {
							echo '<div class="label">Date:</div>';
							echo '<select name="day">';
							$i = 1;
							while($i <= 31) {
								echo '<option value="'.$i.'"';
								if ($i == $eventday) {
									echo ' selected="SELECTED"';
								}
								echo '>'.$i.'</option>';
								$i++;
							}
							echo '</select><select name="month">';
							$i = 1;
							while($i <= 12) {
								echo '<option value="'.$i.'"';
								if ($i == $eventmonth) {
									echo 'selected="SELECTED"';
								}
								echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
								$i++;
							}
							echo '</select><select name="year">';
							$i = date("Y");
							$j = $i+1;
							
							while($i <= $j) {
								echo '<option value="'.$i.'"';
								if ($i == $eventyear) {
									echo ' selected="SELECTED"';
								}
								echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
								$i++;
							}
							echo '</select>';
						}
						else {
							echo '<input type="hidden" name="oldhours" value="'.$eventhours.'" />';
							echo '<input type="hidden" name="oldminutes" value="'.$eventminutes.'" />';
							
							echo '<input type="hidden" name="day" value="'.$eventday.'" />';
							echo '<input type="hidden" name="month" value="'.$eventmonth.'" />';
							echo '<input type="hidden" name="year" value="'.$eventyear.'" />';
							
							echo '<input type="hidden" name="recurrence" value="'.$reoccuring.'" />';
							echo '<input type="hidden" name="frequency" value="'.$frequency.'" />';
							echo '<input type="hidden" name="pattern" value="'.$pattern.'" />';
							
							echo '<input type="hidden" name="endday" value="'.$endday.'" />';
							echo '<input type="hidden" name="endmonth" value="'.$endmonth.'" />';
							echo '<input type="hidden" name="endyear" value="'.$endyear.'" />';
						}
					}
				
	
					if ($eid == 0 && $oid == NULL) {
				?>
						
					<h4>Event Recurrence:</h4>
					<input type="radio" name="recurrence" value="0" <?php if ($reoccuring == 0) { echo 'checked="checked"'; } ?> > 
					This event occurs once, on: 
					<select name="day">
						<?php
							$i = 1;
							while($i <= 31) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 0 && $i == $day) {
									echo ' selected="SELECTED"';
								}
								echo '>'.$i.'</option>';
								$i++;
							}
						?>
					</select>
					<select name="month">
						<?php
							$i = 1;
							while($i <= 12) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 0 && $i == $month) {
									echo 'selected="SELECTED"';
								}
								echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
								$i++;
							}
						?>
					</select>
					<select name="year">
						<?php
							$i = date("Y");
							$j = $i+1;
							
							while($i <= $j) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 0 && $i == $year) {
									echo ' selected="SELECTED"';
								}
								echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
								$i++;
							}
							
						?>
					</select><br /><br /><br />
							
					<input type="radio" name="recurrence" value="1" <?php if ($reoccuring == 1) { echo 'checked="checked"'; } ?> > 
					This event occurs every
						<select name="1frequency">
							<?php
								$i = 1;
								while ($i <= 4) {
									echo '<option value="'.$i.'"';
									if ($reoccuring == 1 && $i == $frequency) {
										echo ' selected="SELECTED"';
									}
									echo '>'.$i.'</option>';
									$i++;
								}
							?>
						</select>
						<select name="1pattern">
							<?php
								$elements = array(8 => "day(s)", 9 => "week(s)", 11 => "year(s)");
								$i = 1;
								foreach ($elements as $key => $element) {
									echo '<option value="'.$key.'"';
									if ($reoccuring == 1 && $key == $pattern) {
										echo ' selected="SELECTED"';
									}
									echo '>'.$element.'</option>';
									$i++;
								}
							?>
						</select><br /><br />
					from 
					<select name="1startday">
						<?php
							$i = 1;
							while($i <= 31) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 1 && $i == $day) {
									echo ' selected="SELECTED"';
								}
								echo '>'.$i.'</option>';
								$i++;
							}
						?>
					</select>
					<select name="1startmonth">
						<?php
							$i = 1;
							while($i <= 12) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 1 && $i == $month) {
									echo 'selected="SELECTED"';
								}
								echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
								$i++;
							}
						?>
					</select>
					<select name="1startyear">
						<?php
							$i = date("Y");
							$j = $i+1;
							
							while($i <= $j) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 1 && $i == $year) {
									echo ' selected="SELECTED"';
								}
								echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
								$i++;
							}
							
						?>
					</select>
					to
					<select name="1endday">
						<?php
							$i = 1;
							while($i <= 31) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 1 && $i == $endday) {
									echo ' selected="SELECTED"';
								}
								echo '>'.$i.'</option>';
								$i++;
							}
						?>
					</select>
					<select name="1endmonth">
						<?php
							$i = 1;
							while($i <= 12) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 1 && $i == $endmonth) {
									echo 'selected="SELECTED"';
								}
								echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
								$i++;
							}
						?>
					</select>
					<select name="1endyear">
						<?php
							$i = date("Y");
							$j = $i+1;
							
							while($i <= $j) {
								echo '<option value="'.$i.'"';
								if ($reoccuring == 1 && $i == $endyear) {
									echo ' selected="SELECTED"';
								}
								echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
								$i++;
							}
							
						?>
					</select><br /><br /><br />
						   
					<input type="radio" name="recurrence" value="2" <?php if ($reoccuring == 2) { echo 'checked="checked"'; } ?> > 
					This event occurs on the
						<select name="2frequency">
							<option value="1">first</option>
							<option value="2">second</option>
							<option value="3">third</option>
							<option value="4">fourth</option>
						</select>
						<select name="2pattern">
							<option value="1">Sunday</option>
							<option value="2" >Monday</option>
							<option value="3">Tuesday</option>
							<option value="4">Wednesday</option>
							<option value="5" >Thursday</option>
							<option value="6">Friday</option>
							<option value="7">Saturday</option>
						</select>
					of every month<br /><br />
					from 
					<select name="2startday">
						<?php
							$i = 1;
							while($i <= 31) {
								echo '<option value="'.$i.'">'.$i.'</option>';
								$i++;
							}
						?>
					</select>
					<select name="2startmonth">
						<option value="1">January</option>
						<option value="2">February</option>
						<option value="3">March</option>
						<option value="4">April</option>
						<option value="5">May</option>
						<option value="6">June</option>
						<option value="7">July</option>
						<option value="8">August</option>
						<option value="9">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>
					<select name="2startyear">
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
					</select>
					to
					<select name="2endday">
						<?php
							$i = 1;
							while($i <= 31) {
								echo '<option value="'.$i.'">'.$i.'</option>';
								$i++;
							}
						?>
					</select>
					<select name="2endmonth">
						<option value="1">January</option>
						<option value="2">February</option>
						<option value="3">March</option>
						<option value="4">April</option>
						<option value="5">May</option>
						<option value="6">June</option>
						<option value="7">July</option>
						<option value="8">August</option>
						<option value="9">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>
					<select name="2endyear">
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
					</select>
					
					<?php
						}
				}
				
				elseif ($mode == 'delete') {
					if ($oid <> NULL) {
						echo '<br /><font size="3">';
						echo 'Delete '.$name.' on '.date('j', $datetime).date('S', $datetime).' '.date('F', $datetime).' ';
						echo date('Y', $datetime).' at '.date('G', $datetime).'.'.date('i', $datetime).date('a', $datetime).'.</font>';
					}
					else
						echo '<br /><font size="3">Delete all occurences of '.$name.' from the calender.';
				}
				
				echo '<br /><br /><br />';
                
                if ($eid == 0 && $oid == NULL) {
                    echo '<input type="submit" name="submit" value="Create event" /> ';
                }
                else {
					if ($mode == 'edit')
                    	echo '<input type="submit" name="submit" value="Update event" /> ';
					elseif ($mode == 'delete')
						echo '<input type="submit" name="submit" value="Delete event" /> ';
                }
                echo '<a class="button" href="calendar.php">Cancel</a>';
                
                ?>
                

            </form>
                    <?php
                    
	
				
			}
		}
	}
?>
</div>

<!--

    <div class="whiteside">
        <a href="index.php"><h4>Admin Home</h4></a>
        
        <h4>Resources</h4>
            <ul class="squashed_list">
                <li><a href="managepictures.php">Manage pictures</a></li>
                <li><a href="#">Audio</a></li>
                <li><a href="#">Notices</a></li>
            </ul>
                            
            <h4>Other pages</h4>
                <ul class="squashed_list">
                    <li><a href="#">Who's who</a></li>
                    <li>Church Activities</li>
                    <li><a href="#">Small groups</a></li>
                    <li><a href="#">Prayer groups</a></li>
                    <li><a href="contact_form.php">Contact Form</a></li>
                </ul>
                
            <h4>Link-Up</h4>
                <ul class="squashed_list">
                    <li><a href="#">Create a new edition</a></li>
                    <li><a href="#">Edit old editions</a></li>
                    <li><a href="#">Archive editions</a></li>
                </ul>
                            
            <h4>Administration</h4>
                <ul class="squashed_list">
                    <li><a href="#">Manage users</a></li>
                </ul>
    </div>
-->

<?php include('../includes/footer.php'); ?>

</div>
</body> 
</html>
