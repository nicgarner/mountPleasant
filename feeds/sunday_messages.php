<?php

include '../includes/connect.php';

$now = date("D, d M Y H:i:s T");

  header("Content-Type: application/rss+xml");

echo "<?xml version=\"1.0\" ?>";
echo "<rss version=\"2.0\">";
	echo "<channel>";
		echo "<title>Mount Pleasant Baptist Church Sunday Messages</title>";
		echo "<link>http://www.mountpleasantchurch.com/sunday_messages</link>";
		echo "<description>Weekly recordings of messages from Mount Pleasant Baptist Church services.</description>";
		echo "<language>en-gb</language>";
		echo "<copyright>Mount Pleasant Baptist Church</copyright>";
		echo "<webmaster>webmaster@mountpleasantchurch.com</webmaster>";
		
		$query  = "SELECT recording_id, name, speaker, comments, ";
		$query .= "DATE_FORMAT(date, '%a, %d %b %Y %T') as pubDate, DATE_FORMAT(date, '%Y_%M') as link ";
		$query .= "FROM recordings ORDER BY DATE_FORMAT(date, '%Y%m%d') DESC, TIME_FORMAT(date, '%H%i') ASC";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		while($row = mysql_fetch_object($result)) {
			echo '<item>';
				echo '<guid>'.$row->recording_id.'</guid>';
				echo '<pubDate>'.$row->pubDate.' +0000</pubDate>';
				echo '<title>'.$row->name.'</title>';
				echo '<description>Speaker: '.$row->speaker.'. '.strip_tags(preg_replace('/<\/p><p>/','. ',$row->comments)).'</description>';
				echo '<link>http://www.mountpleasantchurch.com/sunday_messages?month='.strtolower($row->link).'</link>';
			echo '</item>';
		}
		
	echo '</channel>';
echo '</rss>';

?>
