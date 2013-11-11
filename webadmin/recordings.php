<?php
	
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
            <meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, Jesus, Holy, Spirit, Kettering Road, Paul, Lavender, service, services, Mount, Plesant, town, centre" />
            <meta name="Description" content="Mount Plesant Baptist Church, 147 Kettering Road, Northampton" />
            <link rel="shortcut icon" href="../admin/images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="../admin/images/favicon.ico" type="image/ico" />
            <link rel="stylesheet" media="all" type="text/css" href="../site.css" />

		</head>

		<body>
        
        <div class="site">
            
            <a href="/"><img src="../images/layout/banner.jpg" border="0" /></a>
            
            <div class="menu"><a href="../index.php">Home</a></div>
            
            
                
                <div class="content">
                
                <?php
				
                if (isset($_COOKIE["cookie:mpbcadmin"])) {
				
				?>
                
					<h3>Sunday Messages</h3>
					
    <?php
		
		$id = $_GET['id'];
		$action = $_GET['action'];

		if ($_POST['submit']) {
			$rname		= addslashes($_POST['rname']);
			$speaker	= addslashes($_POST['speaker']);
			$comments	= addslashes($_POST['comments']);
			$length		= $_POST['length'];
			$size		= $_POST['size'];
			$day		= $_POST['day'];
			$month		= $_POST['month'];
			$year		= $_POST['year'];
			$time		= $_POST['time'];
			$filename	= $_POST['filename'];
			$category	= $_POST['category'];
			
/*			$newfile	= $_FILES['newfile'];
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
			if ($length == NULL) {
				$error3  = 1;
				$errors  = 1;
			}
			if ($size == NULL) {
				$error4  = 1;
				$errors  = 1;
			}
/*			if ($id == 0 && $newfile == NULL) {
				$error4 = 1;
				$errors = 1;
			}			
*/			
			if ($errors == 1) {
				echo '<div class="error"><p>There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</p></div><br />';
				$action = 'edit';
			}
			
			else {
				if ($id == 0) {
					$query  = "INSERT INTO recordings (name, speaker, comments, length, size, date, filename, category) ";
					$query .= "VALUES ('$rname', '$speaker', '$comments', '$length', '$size', '$year-$month-$day $time:00', ";
					$query .= "'$filename', '$category')";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$message = 'added';
				}
				else {
					$query  = "UPDATE recordings SET name='$rname', speaker='$speaker', comments='$comments', length='$length', ";
					$query .= "date='$year-$month-$day $time:00', category='$category' WHERE recording_id='$id'";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$message = 'saved';
				}
			}
		}
			
		
			if ($action == 'edit') {
				if (!$_POST['submit']) {
					$query  = "SELECT name, speaker, comments, length, size, filename, category, ";
					$query .= "DATE_FORMAT(date, '%e') as day, DATE_FORMAT(date, '%c') as month, DATE_FORMAT(date, '%Y') as year, ";
					$query .= "TIME_FORMAT(date, '%H:%i') as time ";
					$query .= "FROM recordings WHERE recording_id = '$id'";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					
					while($row = mysql_fetch_object($result)) {
						$rname		= $row->name;
						$speaker	= $row->speaker;
						$comments	= $row->comments;
						$length		= $row->length;
						$size		= $row->size;
						$day		= $row->day;
						$month		= $row->month;
						$year		= $row->year;
						$time		= $row->time;
						$filename	= $row->filename;
						$downloads	= $row->downloads;
						$category 	= $row->category;
					}
				}
						
				echo '<form enctype="multipart/form-data" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'">';
				if ($id == 0)
					echo '<h4>Add recording</h4>';
				else
					echo '<h4>Edit details</h4>';
				echo '<div class="label"><b>Title:</b></div> <input name="rname" size="42" value="'.$rname.'"';
				if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br />';
				echo '<div class="label"><b>Speaker:</b></div> <input name="speaker" size="42" value="'.$speaker.'"';
				if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br />';
				echo '<div class="label"><b>Service:</b></div>';
				
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
				$i = date("Y");
				$j = $i+1;
				while($i <= $j) {
					echo '<option value="'.$i.'"';
					if ($i == $year) {
						echo ' selected="SELECTED"';
					}
					echo '>'.date("Y", strtotime($i.'-1-1')).'</option>';
					$i++;
				}		
				echo '</select> at ';
				
				echo '<select name="time">';
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
				echo '</select><br />';
				
				echo '<div class="label">Comments:</div> <textarea name="comments" cols="50" rows="4">'.$comments.'</textarea><br />';
				
				echo '<div class="label">Category:</div><select name="category">';
					$query  = "SELECT recordings_category_id, name ";
					$query .= "FROM recordings_categories";// ORDER BY priority DESC";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					echo '<option value="0">None</option>';
					while($row = mysql_fetch_object($result)) {
						echo '<option value="'.$row->recordings_category_id.'"';
						if ($category == $row->recordings_category_id) {
							echo ' selected="SELECTED"';
						}
						echo '>'.$row->name.'</option>';
					}
				echo '</select>';
								
				echo '<h4>File details</h4>';
				if (!$id == 0) {
					echo '<div class="label">Filename:</div>';
					echo '<input size="30" value="'.$filename.'" disabled="disabled" /> ';
					echo '<input name="filename" size="30" value="'.$filename.'" type="hidden" />';
					echo '<a href="../resources/recordings/'.$filename.'.mp3" target="_NEW">Listen</a> ';
					echo '<font size="1">(Opens in new window.)</font><br />';
				}
				
/*				if ($id == 0)
					echo '<div class="label"><b>File:</b></div>';
				else
					echo '<div class="label">Replacement file:</div>';
				echo '<input name="newfile" type="file" size="43"';
				if ($error4 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br />';
*/				echo '<div class="label"><b>Length:</b></div> <input name="length" size="1" value="'.$length.'"';
				if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /> minutes<br /><br />';
				echo '<div class="label"><b>Size:</b></div> <input name="size" size="1" value="'.$size.'"';
				if ($error5 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /> KB<br /><br />';
				
				echo '<input type="submit" name="submit" value="Save changes"> ';
				if ($id == 0)
					echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'">Cancel</a>';
				else
					echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'">Discard changes</a>';
	
				echo '</form>';	
			}		
		
			else {
				echo '<font color="#00584c"><b>Administration tools: </b></font>';
				echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'?id=0&action=edit">Add recording</a><br /><br /> ';
				if (!$message == NULL) {
					echo '<div class="confirm"><p>';
					if ($message == 'saved')
						echo 'Your changes have been saved.';
					if ($message == 'added')
						echo 'The recording has been added.';
					if ($message == 'deleted')
						echo 'The recording has been deleted.';	
					echo '</p></div><br />';
				}
				$query  = "SELECT recording_id, name, speaker, downloads, DATE_FORMAT(date, '%W %D %M %Y') as date, ";
				$query .= "TIME_FORMAT(date, '%l.%i %p') as time FROM recordings ";
				$query .= "ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i') ASC";
				
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
											
				while ($row = mysql_fetch_assoc($result)) {
					if ($row['date'] <> $date) {
						echo '</ul><h4>'.$row['date'].'</h4><ul>';
						$date = $row['date'];
					}
					echo '<li><a href="'.$PHP_SELF.'?'.$page.'&id='.$row['recording_id'].'&action=edit">'.$row['name'].'</a>: ';
					echo $row['speaker'].', '.$row['time'].' ('.$row['downloads'].' downloads) ';
					echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row['recording_id'].'&action=delete">delete</a></li>';
				}
				echo '</ul>';
			}
		

		
	?>

</div>

<div class="whiteside">
    <a href="../index.php?page=login"><h4>Admin Home</h4></a>
    
   <? printadminmenu(); ?>

<?php
}
?>
</div>
<?php include('../includes/footer.php'); ?>
</div>
</body> 
</html>
