<?php
	require_once("includes/connect.php");
	header("Content-Type: text/calendar");
	$eventLength = 3600 * 2; //2 hours
	$query  = "SELECT name, location, info, UNIX_TIMESTAMP(datetime) AS datetime, hidetime, event_occurrence_id ";
	$query .= "FROM events, event_occurrences ";
	$query .= "WHERE events.event_id = event_occurrences.event_id AND event_occurrences.deleted = '0' ";
	$query .= "AND datetime < DATE_ADD(now(), INTERVAL 6 MONTH) AND datetime > DATE_SUB(now(), INTERVAL 1 MONTH) ";
	$query .= "ORDER BY datetime ASC, name ASC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	echo "BEGIN:VCALENDAR\r\n";
	echo "VERSION:2.0\r\n";
	echo "PRODID:-//Mount Pleasant Church//NONSGML Church Website Calendar//EN\r\n";
	while($row = mysql_fetch_object($result))
	{
		echo "BEGIN:VEVENT\r\n";
		echo "UID:".$row->event_occurrence_id."@mountpleasantchurch.com\r\n";
		$dateTime = gmdate('Ymd\THis', $row->datetime);
		echo "DTSTART:".$dateTime."Z\r\n";
		$dateTime = gmdate('Ymd\THis', $row->datetime + $eventLength);
		echo "DTEND:".$dateTime."Z\r\n";
		echo "SUMMARY:".$row->name."\r\n";
		echo "LOCATION:".$row->location."\r\n";
		echo "DESCRIPTION:http://".$_SERVER['HTTP_HOST']."/".$row->info."\r\n";
		echo "END:VEVENT\r\n";
	}
	echo "END:VCALENDAR\r\n";
?>