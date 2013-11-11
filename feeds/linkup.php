<?php

include '../includes/connect.php';

$now = date("D, d M Y H:i:s T");

  header("Content-Type: application/rss+xml");

echo "<?xml version=\"1.0\" ?>";
echo "<rss version=\"2.0\">";
	echo "<channel>";
		echo "<title>Mount Pleasant Baptist Church Link-Up Magazine</title>";
		echo "<link>http://www.mountpleasantchurch.com/link_up</link>";
		echo "<description>Monthly church magazine from Mount Pleasant Baptist Church. Registration is required to read some articles.</description>";
		echo "<language>en-gb</language>";
		echo "<copyright>Mount Pleasant Baptist Church</copyright>";
		echo "<webmaster>webmaster@mountpleasantchurch.com</webmaster>";
		
		$query  = "SELECT DATE_FORMAT(edition, '%M') as title, DATE_FORMAT(edition, '%m-%Y') as link, ";
		$query .= "description, linkup_feed_id, DATE_FORMAT(datetime, '%a, %d %b %Y %T') as pubDate ";
		$query .= "FROM linkup_feed ORDER BY datetime DESC";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		while($row = mysql_fetch_object($result)) {
			echo '<item>';
				echo '<guid>'.$row->linkup_feed_id.'</guid>';
				echo '<pubDate>'.$row->pubDate.' +0000</pubDate>';
				echo '<title>'.$row->title.' Link-Up</title>';
				echo '<description>'.$row->description.'</description>';
				echo '<link>http://www.mountpleasantchurch.com/link_up?edition='.$row->link.'</link>';
			echo '</item>';
		}
		
	echo '</channel>';
echo '</rss>';

?>
