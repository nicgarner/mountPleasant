<?php 

global $page;
global $role;

$action = $_GET['action'];
$confirm = $_GET['confirm'];
$id = $_GET['id'];
$catdate = $_GET['date'];
$series = $_GET['series'];
$catspeaker = $_GET['speaker'];
$filter = $_GET['category'];

if ($action == 'delete') {
	if ($confirm == 'true') {

		$query  = "UPDATE recordings SET deleted='1' WHERE recording_id = '$id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		$action = NULL;
		$confirm = NULL;
		$message = 'deleted';
	}
}


if ($_POST['submit']) {
	$rname		= addslashes($_POST['rname']);
	$speaker	= addslashes($_POST['speaker']);
	$reading	= addslashes($_POST['reading']);
	$comments	= addslashes($_POST['comments']);
	$minutes	= $_POST['minutes'];
	$seconds	= $_POST['seconds'];
		$length = '00:'.$minutes.':'.$seconds;
	$size		= $_POST['size'];
	$day		= $_POST['day'];
		if(strlen($day)==1) $day='0'.$day;
	$month		= $_POST['month'];
	$year		= $_POST['year'];
	$time		= $_POST['time'];
	$filename	= $_POST['filename'];
	$category	= $_POST['category'];
		if ($category == 'newSeries') $newSeriesName = $_POST['newSeriesName'];
	
/*	$newfile	= $_FILES['newfile'];
	if ($newfile['error'] == 4) {
		$newfile = NULL;
	}
	else {
		$filename = $year.$month.$day.'_'.preg_replace("/[^0-9]/i","",$time);
		$size  = (($newfile['size'])/1024);
		$type  = $newfile['type'];
		
		if ($type == 'audio/mpeg') {
			$extension = '.mp3';
			$location = "../resources/recordings/".$filename.$extension;
			if (!move_uploaded_file($newfile['tmp_name'], $location) == 1) {
				$errors = 1;
				$error5 = 1;
				$image = NULL;
				echo 'problem copying file';
			}
		}
		else 
			echo 'not an mp3';
	}
*/
	$filename = $year.$month.$day.'_'.preg_replace("/[^0-9]/i","",$time);
	
	if ($rname == NULL) {
		$error1 = 1;
		$errors = 1;
	}
	if ($speaker == NULL) {
		$error2 = 1;
		$errors = 1;
	}
	if ($minutes == NULL || $seconds == NULL) {
		$error3  = 1;
		$errors  = 1;
	}
	if ($size == NULL) {
		$error5  = 1;
		$errors  = 1;
	}
/*	if ($id == 0 && $newfile == NULL) {
		$error4 = 1;
		$errors = 1;
	}			
*/			
	if ($errors == 1)
		$action = 'edit';
	
	else {
		if ($id == 0) {
			if ($category == 'newSeries') {
				$query  = "INSERT INTO recordings_categories (name, shortname) ";
				$query .= "VALUES ('$newSeriesName', '".strtolower(preg_replace("/ /", "_", $newSeriesName))."')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$category = mysql_insert_id();
			}
			$query  = "INSERT INTO recordings (name, speaker, reading, comments, length, size, date, filename, category) ";
			$query .= "VALUES ('$rname', '$speaker', '$reading', '$comments', '$length', '$size', '$year-$month-$day $time:00', ";
			$query .= "'$filename', '$category')";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			$message = 'added';
		}
		else {
			$query  = "UPDATE recordings SET name='$rname', speaker='$speaker', reading='$reading', comments='$comments', ";
			$query .= "length='$length', date='$year-$month-$day $time:00', category='$category' WHERE recording_id='$id'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			$message = 'saved';
		}
	}
}

if ($action == 'edit') {
	echo '<div class="box">';

		if ($id == 0)
			echo '<div class="big">Add recording</div>';
		else
			echo '<div class="big">Edit recording</div>';
		if (! $_POST['submit']) {
			if ($id == '0') {
				if (ini_get(file_uploads)==1) {
					if (substr_replace(ini_get(upload_max_filesize),"",-1)<12 || substr_replace(ini_get(post_max_size),"",-1)<12)
						$default = 'ftp';
					else
						$default = 'form';
				}
				else
					$default = 'onlyftp';
				if ($default=='form') {
					// upload form calls upload.php into an invisible iFrame so it looks like AJAX
					echo '<form action="includes/ajax/mp3Upload.php" method="post" 
							enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >';
					echo '<p id="upload_process" class="upload_process">';
						echo 'Uploading file...<br/><img src="images/layout/loader.gif" /><br/>';
					echo '</p>';
					echo '<div id="upload_form_msg"><br/><p>';
						echo '<strong>Select the mp3 file on your computer, then click Upload.</strong><br/>';
						echo '<span class="small">Note: The maximum size for file uploads is ';
							// calculate upload_max_filesize and post_max_size in bytes
							$upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
							$post_max_size = return_bytes(ini_get('post_max_size'));

							if ($upload_max_filesize <= $post_max_size)
								echo $upload_max_filesize/1024/1024;
							else
								echo $post_max_size/1024/1024;
						echo 'MB. ';
						echo 'If your file is larger than this, please use the ';
						echo '<a href="#">FTP upload method</a>.</span></p></div>';
					echo '<div class="upload_form" id="upload_form">';
						echo '<p><input name="myfile" id="myfile" type="file" size="30" /></p>';
						echo '<input type="submit" name="submitBtn" value="Upload" /> ';
						echo '<a class="button" href="'.$PHP_SELF.'?'.$page.'">Cancel</a>';
					echo '</div>';
					echo '<iframe id="upload_target" name="upload_target" src="includes/ajax/blank.htm" ';
	#					echo 'style="width:600px;height:200px;border:1px solid #000;">';
						echo 'style="width:0;height:0;border:0px solid #fff;">';
					echo '</iframe>';
					echo '</form>';
				}
				else {
					echo '<form action="includes/ajax/mp3Upload.php" method="post" 
							enctype="multipart/form-data" target="upload_target" >';
					echo '<div id="upload_form_msg"><br/>';
					if($default=='onlyftp')
						echo '<p class="warning">File uploading is disabled on this server.</p>';
					echo '<div id="find_file_msg"><p>Please upload the recording to the target folder via ftp. Once the file has ';
					echo 'been uploaded, select the date and time of the service the recording is from, then click Continue.</p>';
					echo '<div id="error"></div></div>';
					echo 'Service: ';
					echo '<select name="FTPday" id="FTPday">';
					$i = 1;
					while($i <= 31) {
						echo '<option value="'.$i.'"';						
							if ($i == date('d'))
								echo ' selected="SELECTED"';
						echo '>'.$i.'</option>';
						$i++;
					}
					echo '</select>';
					
					echo '<select name="FTPmonth" id="FTPmonth">';
					$i = 1;
					while($i <= 12) {
						echo '<option value="'.date("m", strtotime('2004-'.$i.-'1')).'"';
							if ($i == date('m'))
								echo ' selected="SELECTED"';
						echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
						$i++;
					}
					echo '</select>';
					
					// get earliest year of recordings in database to populate the year field
					$query = "SELECT DATE_FORMAT(MIN(date), '%Y') as minYear FROM recordings";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					if (mysql_num_rows($result) > 0) {
						while($row = mysql_fetch_object($result))
							$minYear = $row->minYear;
					}
					if ($minYear == NULL)
						$minYear = date("Y");
					$maxYear = date("Y")+1;
					
					echo '<select name="FTPyear" id="FTPyear">';
					
					while($minYear <= $maxYear) {
						echo '<option value="'.$minYear.'"';
							if ($minYear == date('Y'))
								echo ' selected="SELECTED"';
						echo '>'.date("Y", strtotime($minYear.'-1-1')).'</option>';
						$minYear++;
					}		
					echo '</select> at ';
					
					echo '<select name="FTPtime" id="FTPtime">';
						echo '<option value="10:30">10.30 am</option>';
						echo '<option value="16:00">4.00 pm</option>';
						echo '<option value="18:30">6.30 pm</option>';
						echo '<option value="19:30">7.30 pm</option>';
					echo '</select><br class="clear" /><br/>';

					echo '<input type="submit" name="submitBtn" value="Continue" /> ';
					echo '<a class="button" href="'.$PHP_SELF.'?'.$page.'">Cancel</a></div>';
					echo '<iframe id="upload_target" name="upload_target" src="includes/ajax/blank.htm" ';
#						echo 'style="width:600px;height:200px;border:1px solid #000;">';
						echo 'style="width:0;height:0;border:0px solid #fff;">';
					echo '</iframe>';
					echo '</form>';
				}
			}
			else {
				$query  = "SELECT name, speaker, reading, comments, length, size, filename, category, ";
				$query .= "DATE_FORMAT(date, '%e') as day, DATE_FORMAT(date, '%c') as month, DATE_FORMAT(date, '%Y') as year, ";
				$query .= "TIME_FORMAT(date, '%H:%i') as time ";
				$query .= "FROM recordings WHERE recording_id = '$id'";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				
				while($row = mysql_fetch_object($result)) {
					$rname		= $row->name;
					$speaker	= $row->speaker;
					$reading	= $row->reading;
					$comments	= $row->comments;
					$length		= $row->length;
						$length = explode(':',$length);
						$minutes = $length[1];
						$seconds = $length[2];
					$size		= $row->size;
					$day		= $row->day;
					$month		= $row->month;
					$year		= $row->year;
					$time		= $row->time;
					$filename	= $row->filename;
					$downloads	= $row->downloads;
					$category 	= $row->category;
				}
				if (!$catdate==NULL) $mfilter = '&date='.$year.'_'.strtolower(date("F",mktime(0,0,0,$month,1,2004)));
				elseif (!$series==NULL) $mfilter = '&series='.$series;
				elseif (!$catspeaker==NULL) $mfilter = '&speaker='.strtolower(str_replace(" ", "_", $catspeaker));
			}
		}

		echo '<form id="form" enctype="multipart/form-data" method="post" action="'.$PHP_SELF.'?'.$page.$mfilter.'&id='.$id.'">';
		if ($errors == 1) {
			if (!$id == '0') {
				echo '<br/><div class="error"><p>There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</p></div>';
			}
		}
		if ($id == '0') {
			if ($errors == 1) {
				echo '<br/><div class="error"><p>There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</p></div>';
			}
			else {
				echo '<script language="javascript" type="text/javascript">';
					echo 'document.getElementById(\'form\').style.display=\'none\'';
				echo '</script>';
			}
		}
		echo '<div class="label"><b>Title:</b></div> <input name="rname" id="rname" size="42" value="'.$rname.'"';
		if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br class="clear" />';
		
		echo '<div id="category_area"'; if ($category == 'newSeries') echo ' class="formwarning"';
		echo '><div class="label">Series:</div><select name="category" id="category">';
			$query  = "SELECT recordings_category_id, name ";
			$query .= "FROM recordings_categories ORDER BY recordings_category_id DESC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			echo '<option value="0">None</option>';
			if ($category == 'newSeries')
				echo '<option value="newSeries" selected="SELECTED">'.$newSeriesName.'</option>';
			while($row = mysql_fetch_object($result)) {
				echo '<option value="'.$row->recordings_category_id.'"';
				if ($category == $row->recordings_category_id) {
					echo ' selected="SELECTED"';
				}
				echo '>'.$row->name.'</option>';
			}
		echo '</select>';
		if ($category == 'newSeries')
			echo '<input name="newSeriesName" value="'.$newSeriesName.'" type="hidden" /> Note: A new category will be created.';
		echo '</div><br class="clear" />';
		
		echo '<div class="label">Bible passage(s):</div> <input name="reading" id="reading" size="42" value="'.$reading.'" />';
		echo '<div class="small">Separate multiple bible passages with a comma, for example "John 3:1-16, Mark 1:1-14"</div><br class="clear" />';
		
		echo '<div class="label"><b>Speaker:</b></div> <input name="speaker" id="speaker" size="42" value="'.$speaker.'"';
		if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br class="clear" />';
		
		echo '<div class="label"><b>Service:</b></div>';
		echo '<select name="day" id="day">';
		$i = 1;
		while($i <= 31) {
			echo '<option value="'.$i.'"';
			if ($id == 0) {
				if ($day == NULL) {
					if ($i == date('d'))
						echo ' selected="SELECTED"';
				}
				else {
					if ($i == $day || $day == '0'.$i)
						echo ' selected="SELECTED"';
				}
						
			}
			elseif ($i == $day)
				echo ' selected="SELECTED"';
			echo '>'.$i.'</option>';
			$i++;
		}
		echo '</select>';
		
		echo '<select name="month" id="month">';
		$i = 1;
		while($i <= 12) {
			echo '<option value="'.date("m", strtotime('2004-'.$i.-'1')).'"';
			if ($id == 0) {
				if ($month == NULL) {
					if ($i == date('m'))
						echo ' selected="SELECTED"';
				}
				else {
					if ($i == $month)
						echo ' selected="SELECTED"';
				}
			}
			elseif ($i == $month)
				echo 'selected="SELECTED"';
			echo '>'.date("F", strtotime('2004-'.$i.-'1')).'</option>';
			$i++;
		}
		echo '</select>';
		
		// get earliest year of recordings in database to populate the year field
		$query = "SELECT DATE_FORMAT(MIN(date), '%Y') as minYear FROM recordings";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_object($result))
				$minYear = $row->minYear;
		}
		if ($minYear == NULL)
			$minYear = date("Y");
		$maxYear = date("Y")+1;
		
		echo '<select name="year" id="year">';
		
		while($minYear <= $maxYear) {
			echo '<option value="'.$minYear.'"';
			if ($id == 0) {
				if ($year == NULL) {
					if ($year == date('Y'))
						echo ' selected="SELECTED"';
				}
				else {
					if ($year == $minYear)
						echo ' selected="SELECTED"';
				}
			}
			elseif ($minYear == $year)
				echo ' selected="SELECTED"';
			echo '>'.date("Y", strtotime($minYear.'-1-1')).'</option>';
			$minYear++;
		}		
		echo '</select> at ';
		
		echo '<select name="time" id="time">';
			echo '<option value="10:30"';
				if ($time == "10:30")
					echo ' selected="SELECTED"';
			echo '>10.30 am</option>';
			echo '<option value="16:00"';
				if ($time == "16:00")
					echo ' selected="SELECTED"';
			echo '>4.00 pm</option>';
			echo '<option value="18:30"';
				if ($time == "18:30")
					echo ' selected="SELECTED"';
			echo '>6.30 pm</option>';
			echo '<option value="19:30"';
				if ($time == "19:30")
					echo ' selected="SELECTED"';
			echo '>7.30 pm</option>';
		echo '</select><br class="clear" />';
		
		echo '<div class="label">Comments:</div><textarea name="comments" id="comments" cols="50" rows="4">'.$comments.'</textarea>';
		echo '<div class="label"> </div><div class="label"> </div>';
		echo '<div class="small">For example, information about a guest speaker or anything else of note about the service or message.</div><br class="clear" />';
		
		echo '<h4>File details</h4>';
		if (!$id == 0) {
			echo '<div class="label">Filename:</div>';
			echo '<input size="30" value="'.$filename.'" disabled="disabled" /> ';
			echo '<input name="filename" size="30" value="'.$filename.'" type="hidden" />';
			echo '<a href="../resources/recordings/'.$filename.'.mp3" target="_NEW">Listen</a> ';
			echo '<font size="1">(Opens in new window.)</font><br />';
		}
		
	/*	if ($id == 0)
			echo '<div class="label"><b>File:</b></div>';
		else
			echo '<div class="label">Replacement file:</div>';
		echo '<input name="newfile" type="file" size="43"';
		if ($error4 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br />';
	*/	echo '<div class="label"><b>Length:</b></div> <input name="minutes" id="minutes" size="1" value="'.$minutes.'"';
		if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /> minutes ';
		echo '<input name="seconds" id="seconds" size="1" value="'.$seconds.'"';
		if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /> seconds<br /><br />';
		echo '<div class="label"><b>Size:</b></div> <input name="size" id="size" size="2" value="'.$size.'"';
		if ($error5 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /> KB<br /><br />';
		
		echo '<input type="submit" name="submit" value="Save Changes"> <a class="button" href="'.$PHP_SELF.'?'.$page.$mfilter.'">';
		if ($id == 0)
			echo 'Cancel';
		else
			echo 'Discard changes';
		echo '</a>';
		echo '</form>';
	echo '</div>';
}

else {
	if (!$filter == NULL) {
		if ($filter == "date") {
			$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year FROM recordings WHERE deleted = '0' ";
			$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') ASC LIMIT 0, 1";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$firstMonth = $row->month;
				$firstYear  = $row->year;
			}
			
			$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year FROM recordings WHERE deleted = '0'";
			$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC LIMIT 0, 1";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$lastMonth = $row->month;
				$lastYear  = $row->year;
			}
			
			$year = $lastYear;
			
			echo '<h4>Browse by date:</h4>';
			echo '<p> </p>';
			while ($year >= $firstYear) {
				echo '<div class="smallLabel"><b>' . date('Y', mktime(0,0,0,1,1,$year)) . ': </b></div>';
				if ($year == $firstYear) $month = $firstMonth; else $month = '01';
				if ($year == $lastYear)	$end = $lastMonth; else $end = '12';
				echo '<div class="smallItem">';
					while ($month <= $end) {
						echo '<a href="'.$PHP_SELF.'?'.$page.'&date='.date('Y', mktime(0,0,0,1,1,$year));
						echo '_'.strtolower(date('F', mktime(0,0,0,$month,1,2004))).'">';
							echo date('M', mktime(0,0,0,$month,1,2004));
						echo '</a>';
						if ($month < $end)
							echo ' | ';
						$month++;
					}
				echo '</div>';
				$year--;
			}
		}
		elseif ($filter == "series") {
			echo '<h4>Browse by series:</h4>';
			echo '<p> </p>';
			
			$query  = "SELECT name, shortname, ";
				$query .= "(SELECT DATE_FORMAT(MIN(date), '%D %M %Y') FROM recordings ";
				$query .= "WHERE recordings.category = recordings_categories.recordings_category_id AND deleted='0') AS minDate, ";
				$query .= "(SELECT DATE_FORMAT(MAX(date), '%D %M %Y') FROM recordings ";
				$query .= "WHERE recordings.category = recordings_categories.recordings_category_id AND deleted='0') AS maxDate, ";
				$query .= "(SELECT COUNT(recording_id) FROM recordings ";
				$query .= "WHERE recordings.category = recordings_categories.recordings_category_id AND deleted='0') AS sermonCount "; 
			$query .= "FROM recordings_categories ";
			$query .= "ORDER BY recordings_category_id DESC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				$colbreak = ceil((mysql_num_rows($result))/2);
				echo '<div class="column">';
				while($row = mysql_fetch_object($result)) {
					echo '<p><b><a href="'.$PHP_SELF.'?'.$page.'&series='.$row->shortname.'">'.$row->name.'</a></b> ';
					echo '<span class="small"> ('.$row->sermonCount.')</span><br/>';
					echo '<span class="small">'.$row->minDate.' to '.$row->maxDate.'</span></p>';
					$column++;
					if ($column == $colbreak) { echo '</div><div class="column">'; $column = 0; }
				}
				echo '</div>';
			}
			else
				echo '<p>There are no series to choose from at the moment.</p>';
		}
		elseif ($filter == "speaker") {
			echo '<h4>Browse by speaker:</h4>';
			echo '<p> </p>';
			
			$query  = "SELECT DISTINCT speaker, COUNT(speaker) as number ";
			$query .= "FROM recordings WHERE deleted = '0' ";
			$query .= "GROUP BY speaker ";
			$query .= "ORDER BY COUNT(speaker) DESC, speaker ASC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				$colbreak = ceil((mysql_num_rows($result))/3);
				echo '<div class="column">';
				while($row = mysql_fetch_object($result)) {
					// change the number here if you only want to show speakers with more than a given number of messages
					if($row->number > 0) {
						echo '<p><a href="'.$PHP_SELF.'?'.$page.'&speaker=';
						echo strtolower(str_replace(" ", "_", $row->speaker)).'">'.$row->speaker.'</a>';
						echo '<span class="small"> ('.$row->number.')</span></p>';
						$column++;
						if ($column == $colbreak) { echo '</div><div class="column">'; $column = 0; }
					}
				}
				echo '</div>';
			}
			else
				echo '<p>There are no messages to choose from at the moment.</p>';
		}
		else
			echo 'invalid category';
	}
	else {
		// get the recordings matching the filter
		if (!$catdate == NULL) {
			$monthName = substr_replace(substr($catdate, 5), strtoupper(substr(substr($catdate, 5), 0, 1)), 0, 1);
			
			$year  = substr($catdate, 0, 4);
			$catdate = getmonthnumber(substr($catdate, 5));
			$search = $year.$catdate;
			
			$header = 'Messages from <span class="big">'.$monthName.' '.$year.'</span>';
			$error = 'No recordings were found for this month.';
			
			$query  = "SELECT recording_id, name, speaker, reading, comments, TIME_FORMAT(length, '%i:%s') as length, size, ";
			$query .= "DATE_FORMAT(date, '%W %D %M %Y') as datef, TIME_FORMAT(date, '%l.%i %p') as time, filename ";
			$query .= "FROM recordings ";
			$query .= "WHERE DATE_FORMAT(date, '%Y%m') = '$search' AND deleted = '0' ";
			$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i')";
		}
		elseif (!$series == NULL) {
			$query  = "SELECT recordings_category_id, name FROM recordings_categories WHERE shortname = '$series'";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_object($result)) {
					$series_id = $row->recordings_category_id;
					$series_name = $row->name;
				}
				$query  = "SELECT recording_id, name, speaker, reading, comments, TIME_FORMAT(length, '%i:%s') as length, size, ";
				$query .= "DATE_FORMAT(date, '%W %D %M %Y') as datef, TIME_FORMAT(date, '%l.%i %p') as time, filename ";
				$query .= "FROM recordings ";
				$query .= "WHERE category = '$series_id' AND deleted = '0' ";
				$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i')";
			}
			$header = 'Messages in the series <span class="big">'.$series_name.'</span>';
			$error = 'No recordings were found for this series.';
				
		}
		elseif (!$catspeaker == NULL) {
			$catspeaker = strtolower(str_replace("_", " ", $catspeaker));
			
			$query  = "SELECT recording_id, name, speaker, reading, comments, TIME_FORMAT(length, '%i:%s') as length, size, ";
			$query .= "DATE_FORMAT(date, '%W %D %M %Y') as datef, TIME_FORMAT(date, '%l.%i %p') as time, filename ";
			$query .= "FROM recordings ";
			$query .= "WHERE speaker = '$catspeaker' AND deleted = '0' ";
			$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i')";
			
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_object($result)) {
					$catspeaker = $row->speaker;
				}
			}
			
			$header = 'Messages by <span class="big">'.$catspeaker.'</span>';
			$error = 'No recordings were found by this speaker.';
		}
	
		// if there is no filter, get the last 4 weeks worth of recordings
		else {
			$query = "SELECT DATE_FORMAT(MAX(date), '%Y%m%d') AS lastSunday FROM recordings WHERE deleted='0'";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_object($result)) {
					$lastSunday = $row->lastSunday;
				}
			}

			$firstSunday = date('Ymd', mktime(0,0,0,substr($lastSunday,4,2),substr($lastSunday,6,2)-28,substr($lastSunday,0,4)));
			
			$query  = "SELECT recording_id, name, speaker, reading, comments, TIME_FORMAT(length, '%i:%s') as length, size, ";
			$query .= "DATE_FORMAT(date, '%W %D %M %Y') as datef, TIME_FORMAT(date, '%l.%i %p') as time, filename FROM recordings ";
			$query .= "WHERE DATE_FORMAT(date, '%Y%m%d') <= $lastSunday AND DATE_FORMAT(date, '%Y%m%d') > $firstSunday ";
			$query .= "AND deleted = '0' ";
			$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i')";
			
			$header = "<h4>Latest messages</h4>";
		}
		
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
		$i = 1;
		$x = 1;
		
		echo $header;

		if ($role == '1') {
			echo '<br><div class="iconlink"><a href='.$PHP_SELF.'?'.$page.'&id=0&action=edit">';
			echo '<img title="add recording" alt="add recording" src="images/icons/add24.png" border="0" /></a>';
			echo '<div class="text"><a href="'.$PHP_SELF.'?'.$page.'&id=0&action=edit">Add recording</a></div></div>';
			echo '<br class="clear" />';
		}
		if (!$message == NULL) {
			echo '<br><div class="confirm"><p>';
			if ($message == 'saved')
				echo 'Your changes have been saved.';
			if ($message == 'added')
				echo 'The recording has been added.';
			if ($message == 'deleted')
				echo 'The recording has been deleted.';	
			echo '</p></div><br />';
		}
		
		if ($action == 'delete') {
			$queryb  = "SELECT name FROM recordings WHERE recording_id = '$id'";
			$resultb = mysql_query($queryb) or die ('Error in query: $queryb. ' . mysql_error());
			while($row = mysql_fetch_object($resultb))
				$rname		= $row->name;
			
			if (!$catdate==NULL) $mfilter='&date='.$year.'_'.strtolower(date("F",mktime(0,0,0,$catdate,1,2004)));
			elseif (!$series==NULL) $mfilter='&series='.$series;
			elseif (!$catspeaker==NULL) $mfilter='&speaker='.strtolower(str_replace(" ", "_", $catspeaker));
			
			echo '<div class="warning">';
			echo '<p>Are you sure you want to remove the message <b>'.$rname.'</b> from the site?</p><p align="center">';
			echo '<a href="'.$PHP_SELF.'?'.$page.$mfilter.'&id='.$id.'&action=delete&confirm=true">Delete ';
			echo 'message</a> | <a href="'.$PHP_SELF.'?'.$page.$mfilter.'">Cancel</a></p></div>';
		}
		
		if (mysql_num_rows($result) > 0) {
			echo '<p></p>';	
			while($row = mysql_fetch_object($result)) {
			
				if ($row->datef <> $date) {
					if ($x > 1) {
						echo '<br class="clear" /></div>';
					}
					echo '<div class="new_recording_day'.$i.'">';
					$date = $row->datef;
					if ($i == 1) $i++; else $i = 1;
				}
				
				echo '<div class="new_recording">';
					if ($role == 1) {
						if (!$catdate==NULL) $mfilter='&date='.$year.'_'.strtolower(date("F",mktime(0,0,0,$catdate,1,2004)));
						elseif (!$series==NULL) $mfilter='&series='.$series;
						elseif (!$catspeaker==NULL) $mfilter='&speaker='.strtolower(str_replace(" ", "_", $catspeaker));
						echo '<div style="float:right;">';
						echo ' <a class="img_button" href="'.$PHP_SELF.'?'.$page.$mfilter.'&id='.$row->recording_id.'&action=edit">';
						echo '<img src="images/icons/edit16.png" title="edit recording" alt="edit recording" border="0" />';
						echo '</a> ';
						echo '<a class="img_button" href="'.$PHP_SELF.'?'.$page.$mfilter.'&id='.$row->recording_id.'&action=delete">';
						echo '<img src="images/icons/delete16.png" title="delete recording" alt="delete recording" border="0" />';
						echo '</a> ';
						echo '</div>';
					}
					echo '<span class="big">' . $row->name . '</span>';
					echo '<br />';
					echo '<font size="1">'.$row->speaker.', '.$row->datef.' '.strtolower($row->time).'</font>';
					echo '<div style="float:left; margin-top:3px;">';
						echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="114" height="21" id="player" align="middle">
								<param name="allowScriptAccess" value="sameDomain" />
								<param name="allowFullScreen" value="false" />
								<param name="movie" value="player.swf" />
								<param name="menu" value="false" />
								<param name="quality" value="high" />
								<param value="'; if ($i == 1) echo '#E8FFE8'; else echo '#D0EAD0'; echo '" name="bgcolor" />
								<param name="FlashVars" VALUE="filename_var=resources/recordings/'.$row->filename.'.mp3&length_var='.$row->length.'" />
								<embed src="player.swf" menu="false" quality="high" bgcolor="';	if ($i == 1) echo '#E8FFE8'; else echo '#D0EAD0'; echo '" width="114" height="21" name="player" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="filename_var=resources/recordings/'.$row->filename.'.mp3&length_var='.$row->length.'" />
							</object>';
					echo '</div>';
		
					echo '<script>document.write("<div style=\"float:right; font-size:10px; padding-top:18px;\"><a href=\"javascript:\" onclick=\'document.getElementById(\"details' .$x. '\").style.display=\"block\"\'>More details</a></div>")</script>';
					
					echo '<script>document.write("<div id=\"details'.$x.'\" class=\"details\"><p align=\"right\"><a href=\"javascript:\" onclick=\'document.getElementById(\"details' .$x. '\").style.display=\"none\"\'>Less details</a></p>';
					if ($row->reading <> NULL)
						echo '<p>Bible passage: <i>'.$row->reading.'</i></p>';
					if ($row->comments <> NULL)
						echo '<p>'.addslashes($row->comments).'</p>';
					echo '<p><a href=\"http://www.mountpleasantchurch.com/file_download?type=recording&id='.$row->recording_id.'\">';
					echo 'Download audio file</a> ';
					echo 'Size: ' . round(($row->size)/1024,2) . ' MB, Length: '.$row->length.'</p>';
					echo '</div>")</script>';
						
					echo '<noscript>';
					echo '<div id="details'.$x.'" class="details" style="display:block"><br/><br/>';
					if ($row->reading <> NULL)
						echo '<p>Bible passage: <i>'.$row->reading.'</i></p>';
					if ($row->comments <> NULL)
						echo '<p>'.$row->comments.'</p>';
					echo '<p><a href="http://www.mountpleasantchurch.com/file_download?type=recording&id='.$row->recording_id.'">';
					echo 'Download audio file</a> ';
					echo 'Size: ' . round(($row->size)/1024,2) . ' MB, Length: '.$row->length.'</p>';
					echo '</div></noscript>';
	
					echo '<br class="clear" />';
						
					$x++;
						
				echo '</div>';
			}
	
			echo '<br class="clear" />';
			echo '</div>';
		}
		else {
			echo '<p>'.$error.'</p>';
		}
		echo '<div style="clear:both"></div>';
	}
	
	// display browsing options
	echo '<div class="recordings_menu">';
		echo '<div class="item" style="width:200px;"><a href="'.$PHP_SELF.'?'.$page.'">
			<div class="small">SHOW THE</div><div class="big">Latest messages</div></a></div>';
		echo '<div class="item"><a href="'.$PHP_SELF.'?'.$page.'&category=date">
			<div class="small">BROWSE BY</div><div class="big">Date</div></a></div>';
		echo '<div class="item"><a href="'.$PHP_SELF.'?'.$page.'&category=series">
			<div class="small">BROWSE BY</div><div class="big">Series</div></a></div>';
		echo '<div class="item"><a href="'.$PHP_SELF.'?'.$page.'&category=speaker">
			<div class="small">BROWSE BY</div><div class="big">Speaker</div></a></div>';
	echo '</div>';
		
}

?>
