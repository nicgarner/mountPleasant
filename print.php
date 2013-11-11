<?php 

include 'includes/connect.php';

$id      = $_GET['article'];
$lid 	 = $_GET['edition'];
$action  = $_GET['action'];
$confirm = $_GET['confirm'];

if (!$lid == NULL) {
	$date = explode('-', $lid);
	$month = $date[0];
	$year = $date[1];
	
	$date = $year.'-'.$month.'-'.'01';
	
	$query  = "SELECT linkup.title, linkup.category, linkup_categories.name, content, author FROM linkup, linkup_categories ";
	$query .= "WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.date='$date' AND deleted='0' ";
	if (!isset($_COOKIE["cookie:mpbcadmin"])) {
		$query .= " AND linkup.private = '0' ";
	}
	$query .= "ORDER BY linkup_categories.priority ASC, linkup_categories.name ASC";
	
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());	
	
	if (mysql_num_rows($result) > 0) {
		echo '<html><head>';
		echo '<title>Link-Up Magazine '.date('F',mktime(0,0,0,$month,1,2004)).' '.$year.'</title></head><body>';		
		echo '<script type="text/javascript">document.write("<a href=\"javascript:window.print()\">Print this page</a>")</script>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<script type="text/javascript">document.write("<a href=\"javascript:history.go(-1)\">Go back</a>")</script>';
		echo '<link rel="stylesheet" media="all" type="text/css" href="print.css" />';
		echo '<h3>Link-Up Magazine '.date('F',mktime(0,0,0,$month,1,2004)).' '.$year.'</h3>';
		
		while($row = mysql_fetch_object($result)) {
			echo '<h2>'.stripslashes($row->title).'</h2>';
			if ($row->author <> NULL)
				echo '<p>From ' . stripslashes($row->author) . '</p>';
			echo '<p>' . stripslashes(strip_tags($row->content, '<p><a><ul><ol><li><b><i><u><strong><hr><h1><h2><h3><h4><h5><h6><br><big><center><dd><dl><dt><em><img><marquee><menu><small><strike><table><td><th><tr><tt>')) . '</p>';
			echo '<hr>';
		}
	}
	else {
		echo '<html><head>';
		echo '<title>Nothing to print!</title></head><body>';	
		echo 'Nothing to print! ';
		echo '<script type="text/javascript">document.write("<a href=\"javascript:history.go(-1)\">Go back</a>")</script>';
	}
}

elseif (!$id == NULL) {
	$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year, deleted FROM linkup ";
	$query .= "WHERE article_id = $id AND deleted = '0'";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)) {
			$month = $row->month;
			$year = $row->year;
		}
		$query = "SELECT title, content, author, category, private FROM linkup WHERE article_id='$id'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$title    = $row->title;
			$story    = $row->content;
			$author   = $row->author;
			$category = $row->category;
			$private  = $row->private;
		}
		echo '<html><head>';
		echo '<title>'.$title.' (Link-Up Magazine '.date('F',mktime(0,0,0,$month,1,2004)).' '.$year.')</title></head><body>';		
		echo '<script type="text/javascript">document.write("<a href=\"javascript:window.print()\">Print this page</a>")</script>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<script type="text/javascript">document.write("<a href=\"javascript:history.go(-1)\">Go back</a>")</script>';
		echo '<link rel="stylesheet" media="all" type="text/css" href="print.css" />';

		echo '<h3>Link-Up Magazine '.date('F',mktime(0,0,0,$month,1,2004)).' '.$year.'</h3>';
		echo '<h2>'.stripslashes($title).'</h2>';
		if ($author <> NULL)
			echo '<p>From ' . stripslashes($author) . '</p>';
		echo '<p>' . stripslashes(strip_tags($story, '<p><a><ul><ol><li><b><i><u><strong><hr><h1><h2><h3><h4><h5><h6><br><big><center><dd><dl><dt><em><img><marquee><menu><small><strike><table><td><th><tr><tt>')) . '</p>';	
	}
	
	else {
		echo '<html><head>';
		echo '<title>Nothing to print!</title></head><body>';	
		echo 'Nothing to print! ';
		echo '<script type="text/javascript">document.write("<a href=\"javascript:history.go(-1)\">Go back</a>")</script>';
	}	
}

else {
	echo '<html><head>';
	echo '<title>Nothing to print!</title></head><body>';	
	echo 'Nothing to print! ';
	echo '<script type="text/javascript">document.write("<a href=\"javascript:history.go(-1)\">Go back</a>")</script>';
}

?>
