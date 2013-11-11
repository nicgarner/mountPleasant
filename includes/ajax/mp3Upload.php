<?php

	// Edit upload location here
	if ($_SERVER['SERVER_NAME'] == 'localhost')
		$destination_path = 'C:\\xampp\\htdocs\\mp\\resources\\recordings\\'; #local
	else
//		$destination_path = 'D:\\websites\\mountpleasantchurch.com\\wwwroot\\resources\\recordings\\'; #live
		$destination_path = '/var/www/resources/recordings/';
	
	$result = 0;
	
	$name = basename($_FILES['myfile']['name']);
	$target_path = $destination_path . basename($_FILES['myfile']['name']);

	if(move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
		$result = 1;

		require_once('../id3/getid3/getid3.php');
		$getID3 = new getID3;
		$ThisFileInfo = $getID3->analyze($target_path);
		
		$title = @$ThisFileInfo['tags']['id3v2']['title'][0];  // title from ID3v2
		$series = @$ThisFileInfo['tags']['id3v2']['album'][0];  // album from ID3v2
		$speaker = @$ThisFileInfo['tags']['id3v2']['artist'][0];  // artist from ID3v2
		$length = @$ThisFileInfo['playtime_string'];            // playtime in minutes:seconds, formatted string
		$comments = @$ThisFileInfo['tags']['id3v2']['comments'][0];
		$filesize = round(filesize($target_path)/1024,0);
				
		$length = explode(':',$length);
		$minutes = $length[0];
		$seconds = $length[1];
		
		$year = substr($name,0,4);
		$month = substr($name,4,2);
		$day = substr($name,6,2); if (substr($day,0,1) == '0') $day = substr($day,1,1);
		$time = substr($name,9,2).':'.substr($name,11,2);

	}
	else {
		$error = $_FILES['myfile']['error'];
	}
	sleep(1);
?>

<script language="javascript" type="text/javascript">window.top.window.stopUpload(
	<?php
		echo $result;
		if ($result == 1) {
			echo ',"'.$name.'","'.$title.'","'.$series.'","'.$speaker.'","'.$minutes.'","'.$seconds.'","'.$filesize.'"';
			echo ',"'.$year.'","'.$month.'","'.$day.'","'.$time.'","'.$comments.'"';
		}
		else
			echo ',"'.$error.'"';
	?>
);</script>
