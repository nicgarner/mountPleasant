<?php 

	global $page;
	
	$from = date('Y-m-d');
	$to   = date('Y-m-d', mktime(0,0,0,date('m'),date('d')+6,date('Y')));
	
	$query  = "SELECT name, event, logo, background_colour, border_colour, map,
	                  location, short_details, cancelled, full_details, youth_programme_id,
                    TIME_FORMAT(youth_events.time, '%H:%i') as time, 
										TIME_FORMAT(youth_events.end_time, '%H:%i') as end_time, 
	                  DATE_FORMAT(date, '%a %e&nbsp;%b') as date, 
										DATE_FORMAT(end_date, '%a %e&nbsp;%b') as end_date, 
										TIME_FORMAT(youth_programme.time, '%H:%i') as programme_time, 
										TIME_FORMAT(youth_programme.end_time, '%H:%i') as programme_end_time
             FROM youth_events, youth_programme
						 WHERE DATE_FORMAT(date, '%Y-%m-%d') >= '$from' AND 
						       DATE_FORMAT(date, '%Y-%m-%d') <= '$to' AND
									 deleted = 0 AND hide_upcoming = 0 AND
									 youth_events.youth_event_id = youth_programme.youth_event_id
						 ORDER BY DATE_FORMAT(date, '%Y-%m-%d') ASC, time ASC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	if (mysql_num_rows($result) > 0) {
    echo '<div class="tiles">';
    $tile = 1;
    while($row = mysql_fetch_object($result)) {
      echo '<div class="event_tile';
      if ($row->cancelled) echo ' cancelled'; 
      if ($tile == 4) {
        echo ' end';
        $tile = 0;
      }
      echo '"';
      if (!$row->cancelled) {
        echo ' style="background-image:url(images/layout/';
        if ($row->logo != '')
          echo $row->logo;
        else
          echo 'youth';
        echo '_box_'.rand(1,4);
        echo '.gif);"';
      }
      echo '>';
      
      if ($row->cancelled)
        echo '<img src="images/layout/youth_cancelled.gif" alt="cancelled"
                   class="cancel_notice" title="Sorry, this event has had to be cancelled! :(" />';
      
      $title = ($row->event == null ? $row->name : $row->event);
      if (strlen($title) > 15) $title = '<small>' . $title . '</small>';
      
      echo '<h4';
      if ($row->border_colour != '')
        echo ' style="color:#' . $row->border_colour . ';"';
      echo '>' . $title . '</h4>';
      
      echo '<p>';
      if ($row->end_date == null) {
        echo $row->date;
        if ($row->time != null || $row->programme_time != null)
          if ($row->end_time == null && $row->programme_end_time == null)
            echo ' @ ' . ($row->programme_time == null ? $row->time : $row->programme_time);
          else
            echo ', ' . ($row->programme_time == null ? $row->time : $row->programme_time) . '
                  - ' . ($row->programme_end_time == null ? $row->end_time : $row->programme_end_time);
      }
      else {
        $date_parts = explode(' ', $row->date);
        $end_date_parts = explode(' ', $row->end_date);
        if ($date_parts[2] == $end_date_parts[2])
          $row->date = $date_parts[0] . ' ' . $date_parts[1];
        echo $row->date;
        if ($row->time != null || $row->programme_time != null)
          echo ' @ ' . ($row->programme_time == null ? $row->time : $row->programme_time);
        echo ' to ' . $row->end_date;
        if ($row->end_time != null || $row->programme_end_time != null)
          echo ' @ ' . ($row->programme_end_time == null ? $row->end_time : $row->programme_end_time);
      }
      if ($row->location)
        echo '<br/>' . $row->location;
      echo '</p>';
      
      if ($row->short_details != null)
        echo '<p>' . $row->short_details . '</p>';
      if (!$row->cancelled && ($row->full_details || $row->map)) {
        echo '<a href="/youth_programmes?activity='.$row->youth_programme_id.'"';
        if ($row->border_colour != '')
          echo ' style="color:#' . $row->border_colour . ';"';
        echo '><strong>';
        echo ($row->full_details) ? 'More details' : 'View map';
        echo '</strong></a>';
      }
      
      echo '</div>';
      $tile++;
    }
    echo '<br class="clear" />';
    echo '</div>';
  }
  else
    echo 'Nothing scheduled in the next seven days. Check out the programmes using the links below.';

?>
