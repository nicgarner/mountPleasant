<?php
	// Edit upload location here
//	$destination_path = 'C:\\xampp\\htdocs\\mp\\resources\\recordings\\'; #local
//	$destination_path = 'D:\\websites\\mountpleasantchurch.com\\wwwroot\\resources\\recordings\\'; #live
	$destination_path = '/var/www/resources/recordings/';
	echo 'destination_path: '.$destination_path.'<br/>';
	
	$result = 0;
	
	$target_path = $destination_path . basename( $_FILES['myfile']['name']);
	echo 'target_path: '.$target_path.'<br/><br/>';

	if(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
		$result = 1;
		$filesize = round(filesize($target_path)/1024,0); echo 'filesize: '.$filesize.'<br/><br/>';

		$file = fopen($target_path, "r");
#		fseek($file, -128, SEEK_END);
		$tag = fread($file, 3);
		
		if($tag == "ID3") {
			echo 'MP3 file does have ID3 tags!';
#			$data["song"] = trim(fread($file, 30));
#			$data["album"] = trim(fread($file, 30));
#			$data["comment"] = trim(fread($file, 30));
		}
		else {
			echo 'MP3 file does not have ID3 tags!';
		}
	
		fclose($file);
	}
	sleep(1);
?>

<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>);</script>   
