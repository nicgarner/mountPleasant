<?php

$type = $_GET['type'];
$id = $_GET['id'];

if ($id == "" || $type == "") {
	echo "<p>Sorry, the download file was not specified. Please contact <a href=\"mailto:webmaster@mountpleasantchurch.com\">webmaster@mountpleasantchurch.com</a> for help.</p><p><a href=\"javascript:history.back(-1)\">Go back</a></p>";
}

elseif ($type == 'recording') {

	$query  = "SELECT filename FROM recordings ";
	$query .= "WHERE recording_id = '$id'";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	while($row = mysql_fetch_object($result)) {
		$filename = '/var/www/resources/recordings/'.$row->filename.'.mp3';
//		$filename = 'D:\\websites\\mountpleasantchurch.com\\wwwroot\\resources\\recordings\\' . $row->filename . '.mp3';
	}
	
	$query = "UPDATE recordings SET downloads = downloads+1 WHERE recording_id='$id'";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());

	// required for IE, otherwise Content-disposition is ignored
	if(ini_get('zlib.output_compression'))
	  ini_set('zlib.output_compression', 'Off');
	
	// addition by Jorg Weske
	$file_extension = strtolower(substr(strrchr($filename,"."),1));
	
	if (! file_exists($filename)) {
	  echo "<p>Sorry, the download file could not be found. Please contact <a href=\"mailto:webmaster@mountpleasantchurch.com\">webmaster@mountpleasantchurch.com</a> for help.</p><p><a href=\"javascript:history.back(-1)\">Go back</a></p>";
	}
	else {
		switch( $file_extension )
		{
		  case "pdf": $ctype="application/pdf"; break;
		  case "exe": $ctype="application/octet-stream"; break;
		  case "zip": $ctype="application/zip"; break;
		  case "doc": $ctype="application/msword"; break;
		  case "xls": $ctype="application/vnd.ms-excel"; break;
		  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		  case "gif": $ctype="image/gif"; break;
		  case "png": $ctype="image/png"; break;
		  case "jpeg":
		  case "jpg": $ctype="image/jpg"; break;
		  case "mp3": $ctype="audio/mpeg"; break;
		  default: $ctype="application/force-download";
		}
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers 
		header("Content-Type: $ctype");
		// change, added quotes to allow spaces in filenames, by Rajkumar Singh
		header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		echo 'HERE';
		readfile($filename);
		exit();
	}
}
else {
	echo "<p>Sorry, the download file was not specified. Please contact <a href=\"mailto:webmaster@mountpleasantchurch.com\">webmaster@mountpleasantchurch.com</a> for help.</p><p><a href=\"javascript:history.back(-1)\">Go back</a></p>";
}
?>
