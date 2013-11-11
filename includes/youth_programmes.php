<?php 

  global $page;
  global $role;
  
	$programme = $_GET['programme'];
  $programme_date = $_GET['date'];
	$activity  = $_GET['activity'];
	$action    = $_GET['action'];
	$confirm   = $_GET['confirm'];
  
  // set permissions once here and refer to this var throughout
  $admin = ($role == 1 || $role == 4) ? true : false; # admins and youth admins can make changes
	
  if ($admin) {
    if ($action == 'delete') {
      if ($confirm == 'true') {
        $query  = "SELECT DATE_FORMAT(date, '%d %m %Y') as date, shortname
                     FROM youth_programme, youth_events
                    WHERE youth_programme.youth_event_id = youth_events.youth_event_id
                          AND youth_programme_id = '$activity'";
        $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
        if (mysql_num_rows($result) == 1) {
          while($row = mysql_fetch_object($result))
            $programme = get_programme_details($row->shortname, $row->date);
            $redirectPage = $PHP_SELF.'?programme='.$programme['shortname'].'&date='.$programme['date'];
            $query  = "UPDATE youth_programme SET deleted='1' WHERE youth_programme_id = '$activity'";
            $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
            echo '<meta http-equiv="refresh" content="0;url='.$redirectPage.'">';
            echo 'Deleting event...';
            return;
          }
      } // if (confirm = true)
    } // if (action = delete)
    elseif ($action == restore) {
      $query  = "SELECT DATE_FORMAT(date, '%d %m %Y') as date, shortname
                   FROM youth_programme, youth_events
                  WHERE youth_programme.youth_event_id = youth_events.youth_event_id
                        AND youth_programme_id = '$activity'";
      $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
      if (mysql_num_rows($result) == 1)
        while($row = mysql_fetch_object($result)) {
          $programme = get_programme_details($row->shortname, $row->date);
          $redirectPage = $PHP_SELF.'?programme='.$programme['shortname'].'&date='.$programme['date'];
          $query  = "UPDATE youth_programme SET deleted='0' WHERE youth_programme_id = '$activity'";
          $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
          echo '<meta http-equiv="refresh" content="0;url='.$redirectPage.'">';
          echo 'Restoring event...';
          return;
        }
    }
    elseif ($action == cancel) {
      $query  = "SELECT DATE_FORMAT(date, '%d %m %Y') as date, shortname
                   FROM youth_programme, youth_events
                  WHERE youth_programme.youth_event_id = youth_events.youth_event_id
                        AND youth_programme_id = '$activity'";
      $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
      if (mysql_num_rows($result) == 1) {
        while($row = mysql_fetch_object($result))
          $programme = get_programme_details($row->shortname, $row->date);
          $redirectPage = $PHP_SELF.'?programme='.$programme['shortname'].'&date='.$programme['date'];
          $query  = "UPDATE youth_programme SET cancelled='1' WHERE youth_programme_id = '$activity'";
          $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
          echo '<meta http-equiv="refresh" content="0;url='.$redirectPage.'">';
          echo 'Cancelling event...';
          return;
        }
    }
    elseif ($action == uncancel) {
      $query  = "SELECT DATE_FORMAT(date, '%d %m %Y') as date, shortname
                   FROM youth_programme, youth_events
                  WHERE youth_programme.youth_event_id = youth_events.youth_event_id
                        AND youth_programme_id = '$activity'";
      $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
      if (mysql_num_rows($result) == 1)
        while($row = mysql_fetch_object($result)) {
          $programme = get_programme_details($row->shortname, $row->date);
          $redirectPage = $PHP_SELF.'?programme='.$programme['shortname'].'&date='.$programme['date'];
          $query  = "UPDATE youth_programme SET cancelled='0' WHERE youth_programme_id = '$activity'";
          $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
          echo '<meta http-equiv="refresh" content="0;url='.$redirectPage.'">';
          echo 'Uncancelling event...';
          return;
        }
    }
  } // if (role = 1 or 4)
  else
    $action = null;
	
  // if an event has been specified, show it
  if ($activity) {
    if ($_POST['submit'] && $admin) {
# echo '<p>'.print_r($_POST).'</p>';
			$event_id      = stripslashes($_POST['event_id']);
      $date 		     = stripslashes($_POST['date']);
      $event_name    = stripslashes($_POST['event_name']);
			$end_date      = stripslashes($_POST['end_date']);
			$short_details = stripslashes($_POST['short_details']);
			$time 		     = stripslashes($_POST['time']);
			$end_time      = stripslashes($_POST['end_time']);
      $location      = stripslashes($_POST['location']);
			$map 		       = stripslashes($_POST['map']);
			$full_details  = stripslashes($_POST['full_details']);
			$hide_upcoming = stripslashes($_POST['hide_upcoming']);
			$cancelled     = stripslashes($_POST['cancelled']);
			$deleted       = stripslashes($_POST['deleted']);
			
      $errors = array();
      
      // takes a string and returns true if it represents a valid 'dd mm yyyy' date
      function check_date($string) {
        if (preg_match('@[0-9]{2} [0-9]{2} [0-9]{4}@', $string)) {
          $date_parts = explode(" ", $string);
            $day = $date_parts[0]; $month = $date_parts[1]; $year = $date_parts[2];
            if (checkdate($month, $day, $year))
              return true;
        }
        return false;
      }
      // takes a string and returns true if it represents a valid 'hh:mm' time
      function check_time($string) {
        if (preg_match('@[0-9]{2}:[0-9]{2}@', $string)) {
          $time_parts = explode(":", $string);
          $hour = $time_parts[0]; $minute = $time_parts[1];
          if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59)
            return true;
        }
        return false;
      }
      
      // event name must be supplied for a special event
      if ($event_id == 7&& $event_name == NULL)
        $errors['event_name'] = 'You must enter an event name.';
      
      // date must be present and be a valid date
      if ($date == NULL)
        $errors['date'] = 'You must enter a start date.';
      elseif (!check_date($date))
        $errors['date'] = 'Invalid start date.';
      else {
        $date_parts = explode(' ', $date);
        $date_db = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
      }
      // if end date is present, it must be valid and after start date
      if ($end_date)
        if (!check_date($end_date))
          $errors['end_date'] = 'Invalid end date.';
        else {
          $end_date_parts = explode(' ', $end_date);
          $end_date_db = $end_date_parts[2] . '-' . $end_date_parts[1] . '-' . $end_date_parts[0];
          if ($date_db && $end_date_db <= $date_db)
            $errors['end_date'] = 'End date must be after start date.';
        }
      // short details must be present if not a special event
      if ($event_id != 7 && $short_details == NULL)
        $errors['short_details'] = 'You must enter a short description. Consider \'To be confirmed\' or 
                                    \'To be announced\' if you don\'t yet know.';
      // if time is present, it must be valid
      if ($time)
        if (!check_time($time))
          $errors['time'] = 'Invalid start time.';
      // if end time is present, it must be valid and start time must be present and earlier
      if ($end_time)
        if (!check_time($end_time))
          $errors['end_time'] = 'Invalid end time.';
        else
          if (!$time)
            $errors['end_time'] = 'Start time must be supplied if supplying end time.';
          elseif ($end_date == null)
            if ($end_time <= $time)
              $errors['end_time'] = 'End time must be after start time.';
      
      if ($errors)
        $action = 'edit';
      else {
        if ($activity > 0) {
          $query  = "UPDATE youth_programme SET date='$date_db'";
          $query .= ", event=";
            $query .= ($event_name) ? "'".addslashes($event_name)."'" : "(NULL)";
          $query .= ", end_date=";
            $query .= ($end_date_db) ? "'$end_date_db'" : "(NULL)";
          $query .= ", time=";
            $query .= ($time) ? "'$time'" : "(NULL)";
          $query .= ", end_time=";
            $query .= ($end_time) ? "'$end_time'" : "(NULL)";
          $query .= ", location=";
            $query .= ($location) ? "'".addslashes($location)."'" : "(NULL)";
          $query .= ", map=";
            $query .= ($map) ? "'".addslashes($map)."'" : "(NULL)";
          $query .= ", short_details='".addslashes($short_details)."'";
          $query .= ", full_details=";
            $query .= ($full_details) ? "'".addslashes($full_details)."'" : "(NULL)";
          $query .= ", hide_upcoming=";
            $query .= ($hide_upcoming) ? "1" : "0";
          $query .= " WHERE youth_programme_id='$activity'";
          $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
          
          $programme = get_programme_details($event_id, $date);
          
          echo '<meta http-equiv="refresh" content="0;url='.$PHP_SELF.'?programme='.$programme['shortname'].'&date='.$programme['date'].'">';
          echo 'Updating event...';
          return;
        }
        else {
          $query  = "INSERT INTO youth_programme
                     (youth_event_id,date,end_date,event,time,end_time,location,map,short_details,full_details,hide_upcoming) VALUES(";
          $query .= "'$event_id','$date_db',";
          $query .= ($end_date_db) ? "'$end_date_db'," : "(NULL),";
          $query .= ($event_name) ? "'$event_name'," : "(NULL),";
          $query .= ($time) ? "'$time'," : "(NULL),";
          $query .= ($end_time) ? "'$end_time'," : "(NULL),";
          $query .= ($location) ? "'".addslashes($location)."'," : "(NULL),";
          $query .= ($map) ? "'".addslashes($map)."'," : "(NULL),";
          $query .= ($short_details) ? "'$short_details'," : "(NULL),";
          $query .= ($full_details) ? "'".addslashes($full_details)."'," : "(NULL),";
          $query .= ($hide_upcoming) ? "1" : "0";
          $query .= ")";
          $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
          
          $programme = get_programme_details($event_id, $date);
          
          echo '<meta http-equiv="refresh" content="0;url='.$PHP_SELF.'?programme='.$programme['shortname'].'&date='.$programme['date'].'">';
          echo 'Adding event...';
          return;
        }
                                          
      }
    }
    elseif ($activity > 0) {
      $query = "SELECT event, location, short_details, full_details, cancelled, map, youth_event_id, deleted,
                       TIME_FORMAT(time, '%H:%i') as time, 
                       TIME_FORMAT(end_time, '%H:%i') as end_time, ";
      if ($action == 'edit') {
            $query .= "hide_upcoming, 
                       DATE_FORMAT(date, '%d %m %Y') as date, 
                       DATE_FORMAT(end_date, '%d %m %Y') as end_date ";
      }
      else {
            $query .= "DATE_FORMAT(date, '%e %M %Y') as date, 
                       DATE_FORMAT(end_date, '%e %M %Y') as end_date ";
      }
       $query .= "FROM youth_programme
                 WHERE youth_programme_id = '$activity' ";
      if (!admin)
            $query .= "AND deleted = 0";
      $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
      
      if (mysql_num_rows($result) > 0) {
        while($row = mysql_fetch_object($result)) {
          $event_id      = $row->youth_event_id;
          $event_name    = $row->event;
          $date          = $row->date;
          $end_date      = $row->end_date;
          $short_details = $row->short_details;
          $time          = $row->time;
          $end_time      = $row->end_time;
          $location      = $row->location;
          $map           = $row->map;
          $full_details  = $row->full_details;
          $hide_upcoming = $row->hide_upcoming;
          $cancelled     = $row->cancelled;
          $deleted       = $row->deleted;
        }
      }
      else {
        echo '<div class="error">Sorry, that activity doesn\'t exist. Please try again.</div>';
        return;
      }
    }
    else {
      $query = "SELECT youth_event_id FROM youth_events WHERE shortname = '$programme'";
      $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
      if (mysql_num_rows($result) > 0)
        while($row = mysql_fetch_object($result))
          $event_id = $row->youth_event_id;
      $action = edit;
    }
    if ($action ==  'edit')
      $event = get_programme_details($event_id, '', 'edit');
    else
      $event = get_programme_details($event_id, $date);
    echo $event['header'];
    echo '<p class="right goback"><a href="'.$PHP_SELF.'?programme='.$event['shortname'].'&date='.$event['date'].'">
             Back to '.$event['name'].' programme</a></p>';
    if ($action == 'delete')
      echo '<br class="clear" /><div class="warning"><h4>Delete event</h4>
            <p>If the activity appears in a printed programme or young people are already aware of it,
               it is recommended you cancel the activity instead.</p>
            <p>Are you sure you want to remove this activity from the programme? Note: This action can be 
               undone by returning to the programme and clicking restore.)</p>
            <p><a href="'.$PHP_SELF.'?activity='.$activity.'&action=delete&confirm=true">
                 Yes, remove this event from the programme</a> |
               <a href='.$PHP_SELF.'?programme='.$event['shortname'].'>No, cancel!</a></p></div>';
            
    if ($action ==  'edit') {
      echo '<p><strong>Edit the details and then click Save changes, <br/>
               or click Cancel to loose your changes.</strong></p>';
      if ($programme == 'events' || $event_id == 7)
        echo '<p>Only <em>Event</em> and <em>Date</em> are required; all others are optional.</p>';
      else
        echo '<p>Only <em>Date</em> and <em>Short details</em> are required; all others are optional.</p>';
      
      if ($deleted)
        echo '<div class="warning">This event has been deleted, so changes to it will not be seen
                                   unless you restore it.</div>';
      if ($cancelled)
        echo '<div class="warning">This event has been cancelled, so making changes is probably          
                                   pointless.</div>';
      if ($errors)
        echo '<div class="error">There were some problems with the data. Please check the highlighted
                                 fields and try again.</div>';
      
    ?>
      <form class="edit_youth_event" method="post" action="<?= $PHP_SELF.'?activity='.$activity ?>">
        <? if ($programme == 'events' || $event_id == 7) { ?>
        <div class="field<? if ($errors['event_name']) echo ' form_error'; ?>">
          <? if ($errors['event_name']) { 
               echo '<div class="error_message">' . $errors['event_name'] . '</div>';
             } ?>
          <div class="label">Event:</div>
          <input name="event_name" value="<?= $event_name ?>" size="89" />
          <div class="note">The name of the event.</div>
        </div>
        <? } ?>
        <div class="field<? if ($errors['date'] || $errors['end_date']) echo ' form_error'; ?>">
          <? if ($errors['date'] || $errors['end_date']) { 
               echo '<div class="error_message">';
                 echo $errors['date'] . ' '; 
                 echo $errors['end_date'];
               echo '</div>';
             } ?>
          <div class="label">Date:</div>
          <input name="date" value="<?= $date ?>"  size="10" /> <tt>dd mm yyyy</tt>
          <div class="label">End date:</div>
          <input name="end_date" value="<?= $end_date ?>"  size="10" /> <tt>dd mm yyyy</tt>
          <div class="note">Enter an end date if the event takes place over more than one day.</div>
        </div>
        <div class="field<? if ($errors['short_details']) echo ' form_error'; ?>">
          <? if ($errors['short_details']) { 
               echo '<div class="error_message">' . $errors['short_details'] . '</div>';
             } ?>
          <div class="label">Short details:</div>
          <input name="short_details" value="<?= $short_details ?>" size="89" />
          <div class="note">A very short description of the event that will be displayed in the boxes
                            on the programme page.</div>
        </div>
        <div class="field<? if ($errors['time'] || $errors['end_time']) echo ' form_error'; ?>">
          <? if ($errors['time'] || $errors['end_time']) { 
               echo '<div class="error_message">';
                 echo $errors['time'] . ' '; 
                 echo $errors['end_time'];
               echo '</div>';
             } ?>
          <span class="label">Time:</span>
          <input name="time" value="<?= $time ?>" size="5" /> <tt>hh:mm</tt>
          <span class="label">End time:</span>
          <input name="end_time" value="<?= $end_time ?>" size="5" /> <tt>hh:mm</tt>
          <div class="note">Enter a time and end time if the meeting times are different to normal.
                            Use 24 hour clock.</div>
        </div>
        <div class="field">
          <div class="label">Location:</div>
          <input name="location" value="<?= $location ?>"  size="40" />
          <div class="note">Enter a location if the meeting place is different to normal.</div>
        </div>
        <div class="field">
          <div class="label">Map:</div>
          <input name="map" value='<?= $map ?>' size="89" />
          <div class="note">If you want to show a map of the location, add the 
                            <a href="https://maps.google.co.uk" target="maps" title="opens in new tab">
                            Google Maps</a> code here. Recommended width is 700.</div>
        </div>
        <div class="field">
          <p>If you need to provide extra details, enter them here and they will be
             displayed on a separate page.</p>
          <?
            $CKEditor = new CKEditor();
            $CKEditor->basePath = '/includes/ckeditor/';
            $CKEditor->editor('full_details', stripslashes($full_details));
          ?>
        </div>
        <div class="field">
          <div class="label"><input type="checkbox" name="hide_upcoming"<? if ($hide_upcoming) echo ' checked="CHECKED"'; ?> /></div>
          Don't show this event in the upcoming youth events
        </div>
        
        <input type="hidden" name="event_id" value="<?= $event_id ?>" />
        <input type="hidden" name="cancelled" value="<?= $cancelled ?>" />
        <input type="hidden" name="deleted" value="<?= $deleted ?>" />
        
        <p>
          <input type="submit" name="submit" value="Save changes" />
          <a href="<?= $PHP_SELF.'?programme='.$event['shortname'].'&date='.$event['date'] ?>">Cancel</a>
        </p>
      </form>
 <? } // (if editing)
    else {
      if ($cancelled) {
        echo '<p class="cancelled_notice">Sorry, this event has had to be cancelled! :(</p>';
        echo '<strike>';
      }
      if ($event_o) { # FIX ME!
        echo '<h4 style="font-size:1.2em;';
        if ($event['border_colour'] != '')
          echo ' style="color:#' . $event['border_colour'] . ';';
        echo '">';
        echo ($event['inline_name']) ? $event['inline_name'] : $event['name'];
        echo ': ' . $event . '</h4>';
        
        echo '<p>';
        if ($end_date == null) {
          echo $date;
          if ($time != '00:00')
            echo ' @ ' . $time;
        }
        else {
          $date_parts = explode(' ', $date);
          $end_date_parts = explode(' ', $end_date);
          if ($date_parts[2] == $end_date_parts[2])
            $date = $date_parts[0] . ' ' . $date_parts[1];
          echo $date;
          if ($time != null)
            echo ' @ ' . $time;
          echo ' to ' . $end_date;
          if ($end_time != null)
            echo ' @ ' . $end_time;
        }
        echo '</p>';
      }
      else {
        echo '<h4 style="font-size:1.2em;';
        if ($event['border_colour'] != '')
          echo ' color:#' . $event['border_colour'] . ';';
        echo '">';
        echo ($event['inline_name']) ? $event['inline_name'] : $event['name'];
        echo ': ';
        if ($end_date == null)
          echo $date;
        else {
          $date_parts = explode(' ', $date);
          $end_date_parts = explode(' ', $end_date);
          if ($date_parts[1] == $end_date_parts[1])
            $date = $date_parts[0];
          else
            $date = $date_parts[0] . ' ' . $date_parts[1];
          echo $date . ' to ' . $end_date;
        }

        echo '</h4>';         
        if ($time && $end_time)
          echo '<p>'.$time.' - '.$end_time;
        elseif ($time)
          echo '<p>'.$time;
        if ($location != null) {
          if ($time) echo ', ';
          echo $location;
        }
        echo '</p>';
      }
      
      if ($short_details)
        echo '<p style="font-size:1.4em;"><strong>' . $short_details . '</strong></p>';
      if ($full_details) {
        echo '<strong>Details:</strong><br/>';
        render_content($full_details);
      }
      if ($cancelled) echo '</strike>';
      if ($map)
        echo $map;
    } // (if not editing)
  } // if (event id in url)
  
  // if a programme has been specified, show it
  elseif ($programme) {
    $event = get_programme_details($programme, $programme_date);
    if ($event == false) {
      echo '<div class="error">Sorry, no programme found matching <em>'.$programme.'</em>.</div>';
      return;
    }
    echo $event['header'];
    if ($admin)
      echo '<div class="iconlink"><a href="'.$PHP_SELF.'?activity=new&programme='.$event['shortname'].'" title="add event"><img src="images/icons/add24.png" /></a> <div class="text"><a href="'.$PHP_SELF.'?activity=new&programme='.$event['shortname'].'" title="add event">Add event to '.$event['name'].' programme</a></div></div><br/><br/><br/>';
    $query = "SELECT youth_programme_id, event, location, short_details, full_details, cancelled,
                     TIME_FORMAT(time, '%H:%i') as time, deleted, map,
                     TIME_FORMAT(end_time, '%H:%i') as end_time, 
                     DATE_FORMAT(date, '%e&nbsp;%M') as date, 
                     DATE_FORMAT(end_date, '%e&nbsp;%M') as end_date
                FROM youth_programme
               WHERE youth_event_id = '".$event['id']."' AND
                     DATE_FORMAT(date, '%Y-%m-%d') >= '".date('Y-m-d', $event['from'])."' AND 
                     DATE_FORMAT(date, '%Y-%m-%d') <= '".date('Y-m-d', $event['to'])."' ";
    if (!$admin)
      $query.=      "AND deleted = 0 ";
    $query.=        "ORDER BY deleted ASC, DATE_FORMAT(date, '%Y-%m-%d') ASC, time ASC";
    $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
    if (mysql_num_rows($result) > 0) {
      echo '<div class="tiles">';
      $tile = 1;
      while($row = mysql_fetch_object($result)) {
        if ($row->deleted && $deleted == 0) {
          echo '<br class="clear" />';
          echo '<h4>Deleted events</h4>';
          echo '<p>The following events have been removed from the programme are not public. Click
                   <img src="images/icons/restore.png" /> restore to place the event back in the
                   programme.</p>';
        }
        echo '<div class="event_tile';
        if ($row->cancelled) echo ' cancelled'; 
        if ($tile == 4) {
          echo ' end';
          $tile = 0;
        }
        echo '"';
        if (!$row->cancelled) {
          echo ' style="background-image:url(images/layout/';
          if ($event['logo'] != '')
            echo $event['logo'];
          else
            echo 'youth';
          echo '_box_'.rand(1,4);
          echo '.gif);"';
        }
        echo '>';
        
        if ($admin) {
          echo '<div class="tools">';
            echo '<a href="'.$PHP_SELF.'?activity='.$row->youth_programme_id.'&action=edit">';
            echo '<img src="images/icons/edit16.png" width=16 height=16 title="edit event" /></a>';
            if ($row->cancelled) {
              echo '<a href="'.$PHP_SELF.'?activity='.$row->youth_programme_id.'&action=uncancel">';
              echo '<img src="images/icons/uncancel.png" width=16 height=16 title="uncancel event" /></a>';
            }
            else {
              echo '<a href="'.$PHP_SELF.'?activity='.$row->youth_programme_id.'&action=cancel">';
              echo '<img src="images/icons/delete16.png" width=16 height=16 title="cancel event" /></a>';
            }
            if ($row->deleted) {
              echo '<a href="'.$PHP_SELF.'?activity='.$row->youth_programme_id.'&action=restore">';
              echo '<img src="images/icons/restore.png" width=16 height=16 title="restore event" /></a>';
            }
            else {
              echo '<a href="'.$PHP_SELF.'?activity='.$row->youth_programme_id.'&action=delete">';
              echo '<img src="images/icons/delete.png" width=16 height=16 title="delete event" /></a>';
            }
          echo '</div>';
        }
        
        if ($row->cancelled)
          echo '<img src="images/layout/youth_cancelled.gif" alt="cancelled"
                     class="cancel_notice" title="Sorry, this event has had to be cancelled! :(" />';
        
        if ($row->event) {
          $title = $row->event;
          if (strlen($title) > 15) $title = '<small>' . $title . '</small>';
          echo '<h4';
          if ($event['border_colour'] != '')
            echo ' style="color:#' . $event['border_colour'] . ';"';
          echo '>' . $title . '</h4>';
        
          if ($row->short_details)
            echo '<p>' . $row->short_details . '</p>';
          
          echo '<p>';
          if ($row->end_date == null) {
            echo $row->date;
            if ($row->time != null)
              if ($row->end_time == null)
                echo ' @ ' . $row->time;
              else
                echo ', ' . $row->time . ' - ' . $row->end_time;
          }
          else {
            $date_parts = explode(' ', $row->date);
            $end_date_parts = explode(' ', $row->end_date);
            if ($date_parts[2] == $end_date_parts[2])
              $row->date = $date_parts[0] . ' ' . $date_parts[1];
            echo $row->date;
            if ($row->time != null)
              echo ' @ ' . $row->time;
            echo ' to ' . $row->end_date;
            if ($row->end_time != null)
              echo ' @ ' . $row->end_time;
          }
          echo '</p>';
        }
        else {
          echo '<h4';
          if ($event['border_colour'] != '')
            echo ' style="color:#' . $event['border_colour'] . ';"';
          echo '>';
          if ($row->end_date == null)
            echo $row->date;
          else {
            $date_parts = explode('&nbsp;', $row->date);
            $end_date_parts = explode('&nbsp;', $row->end_date);
            if ($date_parts[1] == $end_date_parts[1])
              $row->date = $date_parts[0];
            else {
              $row->date = $date_parts[0] . '&nbsp;' . substr($date_parts[1],0,3) . '&nbsp;';
              $row->end_date =  '&nbsp;'. $end_date_parts[0] . '&nbsp;' . substr($end_date_parts[1],0,3);
            }
            echo $row->date . '-' . $row->end_date;
          }
          echo '</h4>';
        
          if ($row->short_details)
            echo '<p>' . $row->short_details . '</p>';
          
          if ($row->time && $row->end_time)
            echo '<p>'.$row->time.' - '.$row->end_time;
          elseif ($row->time)
            echo '<p>'.$row->time;
          else
            echo '<p>';
          if ($row->location != null) {
            if ($row->time) echo ', ';
            echo $row->location;
          }
          echo '</p>';
        }
          
        if (!$row->cancelled && ($row->full_details || $row->map)) {
          echo '<a href="'.$PHP_SELF.'?activity='.$row->youth_programme_id.'"';
          if ($event['border_colour'] != '')
            echo ' style="color:#' . $event['border_colour'] . ';"';
          echo '><strong>';
          echo ($row->full_details) ? 'More details' : 'View map';
          echo '</strong></a>';
        }
        echo '</div>';
        $tile++;
        $deleted = $row->deleted; // remember this so can compare to the next one and display
                                  // message about deleted events before the first one
      } // while
      echo '</div>';
    } // if (events were found)
    else {
      echo '<div class="error">';
      echo 'Sorry, no programme details are available at the moment. Please check again soon.</div>';
      return;
    }
  } // if (programme specified)
  
  
    
  // otherwise show index list of programmes
  else {
    echo '<h5 style="padding:0">Youth programmes</h5>';
    echo '<p>Select a group below to view the current programme. Or, <a href="/youth">find out more</a>
             about these activities for young people.</p>';
    $query = "SELECT name, shortname, logo, background_colour, border_colour
              FROM youth_events
              WHERE shortname IS NOT NULL
              ORDER BY weight ASC";
    $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
    if (mysql_num_rows($result) > 0) {
      echo '<div class="tiles">';
      $link = 1;
      while($row = mysql_fetch_object($result)) {
        echo '<a href="'.$PHP_SELF.'?programme='.$row->shortname.'">';
        echo '<div class="tile fourth';
        if ($link == 4) { echo ' end'; $link = 0; }
        echo '">';
        if (file_exists('images/graphics/'.$row->logo.'_logo.jpg'))
          echo '<img src="images/graphics/'.$row->logo.'_logo.jpg" alt="'.$row->name.' logo" />';
        else
          echo '<img src="images/graphics/blank_logo.jpg" alt="'.$row->name.'" />';
        echo '<p style="clear:both"><strong>' . $row->name . '</strong></p>';
        echo '</div></a>';
        $link++;
      } // while (results)
      echo '</div>';
    } // if (results)
    else {
      echo '<div class="error">Sorry, nothing to show here at the moment. Please try again later.</div>';
      return;
    }
  }
  
  function get_programme_details($programme_id, $date = null, $action = null) {
    // if date supplied, check validity and derive timestamp, else set it to the current date
    if ($date) {
      $date = explode('-', $date);
      if (count($date) == 2) {
        if (is_numeric($date[1]) && strlen($date[1]) == 4) {
          if ($date[0] == 'autumn')
            $date = mktime(0,0,0,9,1,$date[1]);
          elseif ($date[0] == 'spring')
            $date = mktime(0,0,0,1,1,$date[1]);
          elseif ($date[0] == 'summer')
            $date = mktime(0,0,0,8,31,$date[1]);
        }
      }
      elseif (count($date) == 3) {
        if ( is_numeric($date[0]) &&
             is_numeric($date[1]) &&
             is_numeric($date[2]) )
          if (checkdate($date[1], $date[2], $date[0]))
            $date = mktime(0,0,0,$date[1],$date[0],$date[2]);
      }
      elseif (count($date) == 1) {
        if (strtotime($date[0]))
          $date = strtotime($date[0]);
        else {
          $date = explode(' ', $date[0]);
          if (count($date) == 3)
            if ( is_numeric($date[0]) &&
                 is_numeric($date[1]) &&
                 is_numeric($date[2]) ) {
              if (checkdate($date[1], $date[0], $date[2]))
                $date = mktime(0,0,0,$date[1],$date[0],$date[2]);
          }
        }
      }
      else
        return false;
    }
    else
      $date = mktime();
    
    // work out the start and end dates of the current term based on current date:
    // first term runs from 1 September to 31 December
    if (date('n', $date) >= 9 && date('n') <= 12) {
      $from = mktime(0,0,0,9,1,date('Y', $date));
      $to   = mktime(0,0,0,12,31,date('Y', $date));
      $programme_date = 'autumn-'.date('Y', $date);
    }
    // second and third term dates are divided by date of Easter Sunday
    else {
      // second term runs from 1 January to Easter Sunday
      if (date('Y-m-d', $date) <= date('Y-m-d', easter_date(date('Y', $date)))) {
        $from = mktime(0,0,0,1,1,date('Y', $date));
        $to   = easter_date(date('Y', $date));
        $programme_date = 'spring-'.date('Y', $date);
      }
      // third term runs from Easter Monday to 31 August
      else {
        $from = mktime(0,0,0,date('m', easter_date(date('Y', $date))),
                             date('d', easter_date(date('Y', $date)))+1,
                             date('Y', $date));
        $to   = mktime(0,0,0,8,31,date('Y', $date));
        $programme_date = 'summer-'.date('Y', $date);
      }
    }
    $programme['from'] = $from;
    $programme['to'] = $to;
    $programme['date'] = $programme_date;
    
    $query = "SELECT youth_event_id, name, logo, time, end_time, blurb, inline_name,
                     background_colour, border_colour, shortname
                FROM youth_events ";
    if (intval($programme_id))
      $query .= "WHERE youth_event_id = '$programme_id'";
    else
      $query .= "WHERE shortname = '$programme_id'";
      
    $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
    if (mysql_num_rows($result) > 0) {
      while($row = mysql_fetch_object($result)) {
        $programme['id'] = $event_id = $row->youth_event_id;
        $programme['name'] = $row->name;
        $programme['shortname'] = $row->shortname;
        $programme['inline_name'] = $row->inline_name;
        $programme['logo'] = $row->logo;
        $programme['time'] = $row->time;
        $programme['end_time'] = $row->end_time;
        $programme['border_colour'] = $row->border_colour;
        
        $programme['header'] = '<div class="tiles">';
        if (file_exists('images/graphics/'.$row->logo.'_logo.jpg'))
        {
          $programme['header'] .= '<div class="tile sixth">';
          $programme['header'] .= '<img src="images/graphics/'.$row->logo.'_logo.jpg" alt="'.$row->name.' logo" /></div>';
        }
        $programme['header'] .= '<div class="tile five-sixths" style="margin-bottom:10px">';
        $programme['header'] .= '<h5 style="margin:0 0 3px 0; line-height:110%;"><span style="color:#'.$row->border_colour.'">';
        $programme['header'] .= '<span style="font-size:1.3em">'.$row->name.' programme</span></span>';
        if ($action != 'edit')
          $programme['header'] .= '<br/><span style="font-size:0.8em">' . date('F', $from) . ' to ' . date('F Y', $to) . '</span>';
        $programme['header'] .= '</h5><p><strong>';
        if ($row->blurb)
          $programme['header'] .= $row->blurb . ' ';
        if ($row->logo)
          $programme['header'] .= '<a href="/youth#'.$row->logo.'" style="color:#'.$row->border_colour.'">Find&nbsp;out&nbsp;more.</a>';
        $programme['header'] .= '</strong></p></div>';
        $programme['header'] .= '<br class="clear" /></div>';
      }
      return($programme);
    }
    else
      return false;
  }
  
?>
