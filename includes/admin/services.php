<?php
		
		$id = $_GET['id'];
		$action = $_GET['action'];
		$confirm = $_GET['confirm'];
		$newServiceDate = $_GET['date'];
    $pagebase = $_SERVER['REDIRECT_URL'].'?tool='.$tool;
    echo '<h3>Services</h3>';
    
		if (($action == 'delete' || $action == 'restore') && $confirm == 'true') {
			$query  = "SELECT DATE_FORMAT(datetime, '%Y') as year, DATE_FORMAT(datetime, '%m') as month, ";
			$query .= "DATE_FORMAT(datetime, '%d') as day, title, ";
			$query .= "TIME_FORMAT(datetime, '%H') as hour, TIME_FORMAT(datetime, '%i') as minute ";
			$query .= "FROM services WHERE service_id = '$id'";
#			echo $query.'<br><br>';
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$year   = $row->year;
				$month  = $row->month;
				$day    = $row->day;
				$hour   = $row->hour;
				$minute = $row->minute;
				$title  = $row->title;
#				echo "$year-$month-$day $hour:$minute<br><br>";
			}
			
			if ($year == 2004 || $year == 0000) {
				$day	= substr($newServiceDate,8,2);
				$month	= substr($newServiceDate,5,2);
				$year	= substr($newServiceDate,0,4);
			
				$query  = "INSERT INTO services (datetime, title) ";
				$query .= "VALUES ('$year-$month-$day $hour:$minute:00', 'CANCELLED')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$message = $action;
			}
			else {
				$query  = "UPDATE services SET deleted='1' ";
				$query .= "WHERE service_id='$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$message = $action;
				
				if ($title <> 'CANCELLED') {
					if ($day < 8)
						$searchDay = '01';
					elseif ($day < 15)
						$searchDay = '08';
					elseif ($day < 22)
						$searchDay = '15';
					elseif ($day < 29)
						$searchDay = '22';
					elseif ($day < 32)
						$searchDay = '29';
					
					$query  = "SELECT service_id FROM services WHERE datetime = '2004-01-$searchDay $hour:$minute:00'";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					if (mysql_num_rows($result) > 0) {
						$query  = "INSERT INTO services (datetime, title) ";
						$query .= "VALUES ('$year-$month-$day $hour:$minute:00', 'CANCELLED')";
						$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					}
					else {
						$query  = "SELECT service_id FROM services WHERE DATE_FORMAT(datetime, '%Y%m%d%H%i') = '00000000$hour$minute'";
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						if (mysql_num_rows($result) > 0) {
							$query  = "INSERT INTO services (datetime, title) ";
							$query .= "VALUES ('$year-$month-$day $hour:$minute:00', 'CANCELLED')";
							$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
						}
					}
				}
			}
			$action = NULL;
			$confirm = NULL;
		}
		
		elseif ($_POST['submit']) {
			$title		= addslashes($_POST['title']);
			$comment	= addslashes($_POST['comment']);
			$speaker	= addslashes($_POST['speaker']);
			$day		= $_POST['day'];
			$month		= $_POST['month'];
			$year		= $_POST['year'];
			$hour		= $_POST['hour'];
			$minute		= $_POST['minute'];
			
/*			$filename = $year.$month.$day.'_'.preg_replace("/[^0-9]/i","",$time);   */
			
			if ($title == NULL) {
				$error1 = 1;
				$errors = 1;
			}
			if ($day == NULL || $month == NULL || $year == NULL) {
				$error2 = 1;
				$errors = 1;
			}
			if ($hour == NULL || $minute == NULL) {
				$error3 = 1;
				$errors = 1;
			}

			if ($errors == 1) {
				echo '<div class="error">There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</div>';
				$action = 'edit';
			}
			else {
				if ($id == 0) {
					$query  = "INSERT INTO services (datetime, title, comment, speaker) ";
					$query .= "VALUES ('$year-$month-$day $hour:$minute:00', '$title', '$comment', '$speaker')";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$message = 'saved';
				}
				else {
					$query  = "UPDATE services SET datetime='$year-$month-$day $hour:$minute:00', title='$title', ";
					$query .= "comment='$comment', speaker='$speaker' WHERE service_id='$id'";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$message = 'saved';
				}
			}
		}
			
		
		if ($action == 'edit') {
			if (!$_POST['submit']) {
				$query  = "SELECT title, comment, speaker, ";
				$query .= "DATE_FORMAT(datetime, '%e') as day, DATE_FORMAT(datetime, '%c') as month, ";
				$query .= "DATE_FORMAT(datetime, '%Y') as year, ";
				$query .= "TIME_FORMAT(datetime, '%H') as hour, TIME_FORMAT(datetime, '%i') as minute ";
				$query .= "FROM services WHERE service_id = '$id'";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				
				while($row = mysql_fetch_object($result)) {
					$title		= $row->title;
					$comment	= $row->comment;
					$speaker	= $row->speaker;
					
					if ($newServiceDate == NULL) {
						$day	= $row->day;
						$month	= $row->month;
						$year	= $row->year;
					}
					else {
						$day	= substr($newServiceDate,8,2);
						$month	= substr($newServiceDate,5,2);
						$year	= substr($newServiceDate,0,4);
					}
					
					$hour		= $row->hour;
					$minute		= $row->minute;
				}
			}
					
			echo '<form enctype="multipart/form-data" method="post" action="'.$pagebase;
			if ($newServiceDate == NULL)
				echo '&id='.$id;
			echo '">';
			if ($id == 0)
				echo '<h4>Add service details</h4>';
			else
				echo '<h4>Edit service details</h4>';
			echo '<br />';
			echo '<div class="label"><b>Service title:</b></div> <input name="title" size="55" value="'.$title.'"';
			if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br class="clear" />';
			echo '<div class="label">Speaker:</div> <input name="speaker" size="55" value="'.$speaker.'" /><br class="clear" />';
			echo '<div class="label">Comment:</div> <input name="comment" size="55" value="'.$comment.'" /><br class="clear" />';
			
			echo '<div class="label"><b>Date:</b></div>';
			
			echo '<select name="day">';
			$i = 1;
			while($i <= 31) {
				echo '<option value="'.$i.'"';
				if ($id == 0) {
					if ($i == date('d'))
						echo ' selected="SELECTED"';
				}
				elseif ($i == $day)
					echo ' selected="SELECTED"';
				echo '>'.$i.'</option>';
				$i++;
			}
			echo '</select>';
			
			echo '<select name="month">';
			$i = 1;
			while($i <= 12) {
				echo '<option value="'.date("m", strtotime('2004-'.$i.-'1')).'"';
				if ($id == 0) {
					if ($i == date('m'))
						echo ' selected="SELECTED"';
				}
				elseif ($i == $month)
					echo 'selected="SELECTED"';
				echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
				$i++;
			}
			echo '</select>';
			
			echo '<select name="year">';
			$i = 2009;
			$j = date("Y")+1;
			while($i <= $j) {
				echo '<option value="'.$i.'"';
				if ($i == $year) {
					echo ' selected="SELECTED"';
				}
				echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
				$i++;
			}		
			echo '</select><br class="clear" />';
			
			echo '<div class="label"><b>Time:</b></div>';
			echo '<input name="hour" maxlength="2" size="1" value="'.$hour.'"';
			if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' />:';
			echo '<input name="minute" maxlength="2" size="1" value="'.$minute.'"';
			if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br class="clear" /><br /><br />';

			echo '<input type="submit" name="submit" value="Save changes"> ';
			if ($id == 0)
				echo '<a class="button" href="'.$pagebase.'">Cancel</a>';
			else
				echo '<a class="button" href="'.$pagebase.'">Discard changes</a>';

			echo '</form>';	
		}
		
		elseif (($action == 'delete' || $action == 'restore') && confirm <> 'true') {
      if ($action == 'delete')
				echo '<h4>Cancel service</h4>';
			else
				echo '<h4>Restore service</h4>';
						
			if ($confirm <> 'true') {
				$query  = "SELECT title, comment, speaker, DATE_FORMAT(datetime, '%W, %D %M %Y ') as date, ";
				$query .= "DATE_FORMAT(datetime, 'at %l:%i %p') as time ";
				$query .= "FROM services WHERE service_id = '$id'";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				while($row = mysql_fetch_object($result)) {
					if ($row->title == 'CANCELLED')
						echo '<p>Are you sure you want to restore the following service?</p>';
					else
						echo '<p>Are you sure you want to cancel the following service?</p>';
					echo '<p><strong>';
					if (!$row->title=='CANCELLED')
						echo '<b>'.$row->title.'</b> on ';
					if ($newServiceDate == NULL)
						echo $row->date;
					else {
						$day	= substr($newServiceDate,8,2);
						$month	= substr($newServiceDate,5,2);
						$year	= substr($newServiceDate,0,4);
						
						echo date("l, j F Y ", mktime(0, 0, 0,$month,$day,$year));
					}
					
					echo strtolower($row->time).'</strong></p>';
					echo '<p><a href="'.$pagebase.'&id='.$id.'&action='.$action.'&confirm=true';
					if($newServiceDate <> NULL)
						echo "&date=".$newServiceDate;
					echo '">Yes, ';
					if ($row->title == 'CANCELLED')
						echo 'restore';
					else
						echo 'cancel';
					echo ' this service</a>';
					echo ' | <a href="'.$pagebase.'">No, go back</a></p>';
					echo '<br><br><br><br><br><br><br><br><br>';
				}
			}
		}		
	
		else {
      echo '<p>Add and edit the details of upcoming services. This information is displayed on the <a href="/">home</a> page and the <a href="/services">services </a> page.<p>';
      echo '<p>To edit the details of a service, click on the service. To "cancel" a service (for example, evening services around Christmas), click the "x" next to the service.</p>';
			if ($message <> NULL) {
				echo '<div class="confirm"><p>';
				if ($message == 'saved')
					echo 'Your changes have been saved.';
				if ($message == 'added')
					echo 'The service has been added.';
				if ($message == 'delete')
					echo 'The service has been cancelled.';
				if ($message == 'restore')
					echo 'The service has been restored.';
				echo '</p></div><br />';
			}
			
			// display service details for servcices on each Sunday of this month and the next four months
			$thisMonth = date("m");
			$thisYear  = date("Y");
			
			$m = 0;
			
			while($m <= 4) {
			
				// calculate the month and year for the current iteration
				$currentMonth = date("m", mktime(0,0,0,$thisMonth+$m,1,$thisYear));
				$currentYear  = date("Y", mktime(0,0,0,$thisMonth+$m,1,$thisYear));
				
				// show the name of the month as title for this iteration
				echo '<div class="big">'.date("F", mktime(0,0,0,$currentMonth,1,$currentYear)).'</div>';
				
				$i = 1;
				while($i <= 7) {
					if (date("w", mktime(0, 0, 0,$currentMonth,$i,$currentYear)) == 0) {
						$firstSunday = $i;
					}
					$i++;
				}
				
				$i = 0;
				while($i <= 31) {
					$thisWeek = $i+1;
					if (strlen($thisWeek) == 1)
						$thisWeek = "0".$thisWeek;
					$thisSundayDate = date("Y-m-d", mktime(0,0,0,$currentMonth,$firstSunday+$i,$currentYear));
					$thisSundayText = date("jS F", mktime(0,0,0,$currentMonth,$firstSunday+$i,$currentYear));
					if ($currentMonth == date("m", mktime(0,0,0,$currentMonth,$firstSunday+$i,$currentYear))) {
						$query  = "SELECT service_id, TIME_FORMAT(datetime, '%l.%i %p') as time, title, comment, speaker, ";
						$query .= "DATE_FORMAT(datetime, '%Y') as type FROM services ";
						$query .= "WHERE deleted = '0' AND (DATE_FORMAT(datetime, '%Y-%m-%d') = '2004-01-$thisWeek' " ;
						$query .= "OR DATE_FORMAT(datetime, '%Y') = '0000' ";
						$query .= "OR DATE_FORMAT(datetime, '%Y-%m-%d') = '$thisSundayDate') ";
						$query .= "ORDER BY TIME_FORMAT(datetime, '%H%i') ASC, DATE_FORMAT(datetime, '%Y-%m-%d') DESC";
						$result = mysql_query($query) or die ('Error in query b: $query. ' . mysql_error());
						
						$lastTime = '0';
						
						echo '<br /><b>Sunday '.$thisSundayText.':</b>';
							echo '<table cellpadding="3">';
							while($row = mysql_fetch_object($result)) {
								
								// if a cancelled service is a repeating one, we don't want to show it
								if ($row->title == 'CANCELLED') {
									if ($row->type <> 2004 && $row->type <> 0000)
										$show = 1;
									else
										$show = 0;
								}
								else
									$show = 1;

								if ($row->time <> $lastTime) {
									if ($show == 1) {
										echo '<tr valign="top"><td width="69" align="right">';
										if ($row->title == 'CANCELLED')
											echo '<font color="gray"><strike>'.strtolower($row->time).'</strike></font>';
										else
											echo '<b>'.strtolower($row->time).'</b>';
										echo '</td><td>';	
										if ($row->title == 'CANCELLED') {
											echo '<font color="gray"><strike>'.$row->title.'</strike> &nbsp;</font>';
											echo '<a href="'.$pagebase.'&id='.$row->service_id.'&action=restore" ';
											echo 'title="restore service">r</a>';
										}
										else {
											echo '<a href="'.$pagebase.'&id='.$row->service_id.'&action=edit';
											if ($row->type == 2004 || $row->type == 0000)
												echo '&date='.$thisSundayDate;
											echo '">';
											echo $row->title.'</a> &nbsp;';
											echo '<a href="'.$pagebase.'&id='.$row->service_id.'&action=delete';
											if ($row->type == 2004 || $row->type == 0000)
												echo '&date='.$thisSundayDate;
											echo '" title="cancel service">x</a>';
											if (! $row->speaker == NULL) {
												echo '<p class="small">Speaker: '.$row->speaker.'</p>';
											}
											if (! $row->comment == NULL) {
												echo '<p class="small">'.$row->comment.'</p>';
											}
										}
									}
									$lastTime = $row->time;
									echo '</td></tr>';
								}
								else
									$lastTime = $row->time;
							}
						echo '</table>';
					}
					$i = $i+7;
				}
				
				$m++;
				echo '<br/><br/>';
			}
		}
		
	?>
<br /><br /><br />


