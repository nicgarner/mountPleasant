<?php 

	include 'connect.php';

	$query = "SELECT name, venue, address1, address2, address3, telephone, TIME_FORMAT(time, '%H:%i') as time, comments, x_pos, y_pos FROM housegroups";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	$i = 1;
	
	while($row = mysql_fetch_object($result)) {
		print '&name'.$i.'='.$row->name.' Housegroup';
		print '&address'.$i.'=';
			if ($row->venue <> NULL) {
				print 'Venue: '.$row->venue.'|';
			}
			if ($row->address1 <> NULL) {
				print '|'.$row->address1.'|';
			}
			if ($row->address2 <> NULL) {
				print $row->address2.'|';
			}
			if ($row->address3 <> NULL) {
				print $row->address3.'|';
			}
			if ($row->telephone <> NULL) {
				print 'Telephone: '.$row->telephone.'|';
			}
			if ($row->time <> NULL) {
				print 'Wednesdays at '.$row->time;
			}
		if ($row->comments <> NULL) {
			print '||'.$row->comments;
		}
		print '&pos_x'.$i.'='.$row->x_pos;
		print '&pos_y'.$i.'='.$row->y_pos;
		$i++;
	}
	
	print '&total='.($i-1);

?>