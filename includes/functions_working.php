<?php

function draw_menu($parent) {
	global $page;
	global $page_parent;
	global $content_id;
	global $role;
	
	$query = "SELECT content_id, name, shortname, parent FROM content WHERE parent = $parent AND in_nav = 1 ORDER BY weight DESC";
	$result = mysql_query($query);
	echo '<ul>';
	while($row = mysql_fetch_object($result)) {
		echo '<li';
		
		if ($page == $row->shortname && $row->parent == 0) {
			echo ' class="selected"';
		}
		elseif ($page_parent <> 0 && $row->content_id == $page_parent) {
			echo ' class="selected"';
		}
		
		echo '>';
		echo '<a href="' . $row->shortname . '">';
		if ($row->name == 'Log in') {
			if ($role == 1)
				echo 'Administration';
			elseif ($role == 2 || $role == 3)
				echo 'My Preferences';
			else
				echo 'Log in';
		}
		else
			echo $row->name;
		if ($row->parent == 0) {
			echo '<!--[if IE 7]><!--></a><!--<![endif]--><!--[if lte IE 6]><table><tr><td><![endif]-->';
		}
		else {
			echo '</a>';
		}

		if ($row->parent == 0) {
			draw_menu($row->content_id);
			echo '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
		}
		else {
			echo '</a>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

function list_menu($parent) {
	global $page;
	global $page_parent;
	global $content_id;
	$query = "SELECT content_id, name, parent, weight FROM content WHERE parent = $parent AND in_nav = 1 ORDER BY weight DESC";
	$result = mysql_query($query);
	while($row = mysql_fetch_object($result)) {			
		if ($row->parent == 0)
			echo '<p><b>';
		echo $row->name;
		if (!$row->parent == 0)
			echo ' (' . $row->weight . '), ';
		if ($row->parent == 0)
			echo ':</b> ';
		if ($row->parent == 0) {
			list_menu($row->content_id);
			echo '</p>';
		}
	}

}

function render_content($content) {
	$links = preg_match_all("@[\[]{2}[a-zA-Z_: |&.!/()\-',0-9]+[\]]{2}@", $content, $matches);

	$replace = array();
	
	foreach ($matches as $link) {
		foreach ($link as $alink) {
		    // php include
			if (substr(trim($alink), 0, 6) == '[[inc:') {
				$end = strpos(trim($alink), ']') - 6;		// find the position of the closing ]
				$include = substr(trim($alink), 6, $end);	// find the text that we actually want to include
				$filename = 'includes/'.$include.'.php';
				if(file_exists($filename)) {
					ob_start();
					include_once($filename);
					$include = ob_get_contents();
					ob_end_clean();
					array_push($replace, $include);
				}
				else
					array_push($replace, '<p>Some content couldn\'t be loaded. Please try again later. <font color="white">[Error: the <i>'.substr(substr($filename,9,50),0,-4).'</i> include couldn\'t be loaded.]</font></p>');
			}
			// image
			elseif (substr(trim($alink), 0, 6) == '[[img:') {
				$end = strpos(trim($alink), ']') - 6;
				$picture = substr(trim($alink), 6, $end);
				
				$query = "SELECT filetype, caption FROM graphics WHERE name = '".$picture."' AND deleted = 0";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) { 
						$picture = '<img src="images/graphics/'.$picture.'.'. $row->filetype .'" title="'. $row->caption .'">';
						array_push($replace, $picture);
					}
				}
                                else 
					array_push($replace, '[picture not found]');
			}
			// icon image
			elseif (substr(trim($alink), 0, 10) == '[[imgicon:') {
				$end = strpos(trim($alink), ']') - 10;
				$data = explode("|",substr(trim($alink), 10, $end));
				$image = $data[0];
				$query = "SELECT filetype, caption FROM graphics WHERE name = '".$image."' AND deleted = 0";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) {
						$picture = '';
						$picture .= '<div class="iconlink">';
						if (array_key_exists(2,$data))
							$picture .= '<a href="'.$data[2].'">';
						$picture .= '<img src="images/graphics/'.$image.'.'. $row->filetype .'" ';
						$picture .= 'title="'. $row->caption .'">';
						if (array_key_exists(2,$data))
							$picture .= '</a>';
						$picture .= '<div class="text">';
						if (array_key_exists(2,$data))
							$picture .= '<a href="'.$data[2].'">';
						$picture .= $data[1];
						if (array_key_exists(2,$data))
							$picture .= '</a>';
						$picture .= '</div></div>';
						array_push($replace, $picture);
					}
				}
                                else
					array_push($replace, '[picture not found]');
			}
      // download link
			elseif (substr(trim($alink), 0, 11) == '[[download:') {
				$end = strpos(trim($alink), ']') - 11;
				$file = explode(".",substr(trim($alink), 11, $end));
				$query = "SELECT filetype, size, link_text FROM files 
                  WHERE name = '".$file[0]."' AND filetype = '".$file[1]."' AND deleted = 0";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) {
						$link = '<div class="iconlink">';
						$link .= '<a href="resources/'.$file[0].'.'.$file[1].'">';
						$link .= '<img src="images/icons/'.$file[1].'_sm.gif" 
                           title="'.$file[0].'.'.$file[1].' ('.round(($row->size)/1024).'kb)" /></a>';
						$link .= '<div class="text">';
            $link .= '<a href="resources/'.$file[0].'.'.$file[1].'"
                         title="'.$file[0].'.'.$file[1].' ('.round(($row->size)/1024).'kb)">';
            $link .= $row->link_text.'</a> ';
						if ($row->filetype == 'pdf')
							$link .= '<small>(Requires <a href="http://get.adobe.com/uk/reader/" target="adobereader">Adobe Reader</a>)</small>';
						$link .= '</div></div>';
						array_push($replace, $link);
					}
				}
                                else
					array_push($replace, '[picture not found]');
			}
			// left floated image
			elseif (substr(trim($alink), 0, 10) == '[[imgleft:') {
				$end = strpos(trim($alink), ']') - 10;
				$picture = substr(trim($alink), 10, $end);
				
				$query = "SELECT filetype, caption FROM graphics WHERE name = '".$picture."' AND deleted = 0";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) { 
						$picture = '<img class="imgleft" src="images/graphics/'.$picture.'.'. $row->filetype .'" title="'. $row->caption .'">';
						array_push($replace, $picture);
					}
				}
				else
					array_push($replace, '[picture not found]');

			}
			// right floated image
			elseif (substr(trim($alink), 0, 11) == '[[imgright:') {
				$end = strpos(trim($alink), ']') - 11;
				$picture = substr(trim($alink), 11, $end);
				
				$query = "SELECT filetype, caption FROM graphics WHERE name = '".$picture."' AND deleted = 0";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) { 
						$picture = '<img class="imgright" src="images/graphics/'.$picture.'.'. $row->filetype .'" title="'. $row->caption .'">';
						array_push($replace, $picture);
					}
				}
				else
					array_push($replace, '[picture not found]');
			}
			// internal page link
			elseif (substr(trim($alink), 0, 2) == '[[') {
				//find the page name
				$link = explode("|", (str_replace(array("[", "]"), " ", trim($alink))));
				$sname = trim($link[0]);
				$query = "SELECT name FROM content WHERE shortname = '".$sname."'";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) { 
					while($row = mysql_fetch_object($result)) 
						$lname = $row->name;
					if ($link[1] == NULL) 
						array_push($replace, '<a href="'.$sname.'">'.$lname.'</a>');
					else
						array_push($replace, '<a href="'.$sname.'">'.trim($link[1]).'</a>');
			}
			else
				array_push($replace, ($link[1] == NULL ? $link[0] : $link[1]));
		}
	}
	
	echo str_replace($matches[0], $replace, $content);
	
}

function getdaynumber($day) {
	if ($day == 'Monday')
		echo '1';
	if ($day == 'Tuesday')
		echo '2';
	if ($day == 'Wednesday')
		echo '3';
	if ($day == 'Thursday')
		echo '4';
	if ($day == 'Friday')
		echo '5';
	if ($day == 'Saturday')
		echo '6';
	if ($day == 'Sunday')
		echo '7';
}

function getmonthnumber($month) {
	if ($month == 'january')
		return '01';
	if ($month == 'february')
		return '02';
	if ($month == 'march')
		return '03';
	if ($month == 'april')
		return '04';
	if ($month == 'may')
		return '05';
	if ($month == 'june')
		return '06';
	if ($month == 'july')
		return '07';
	if ($month == 'august')
		return '08';
	if ($month == 'september')
		return '09';
	if ($month == 'october')
		return '10';
	if ($month == 'november')
		return '11';
	if ($month == 'december')
		return '12';
}

function printadminmenu() {
	echo '<h4>Resources</h4>';
	echo '<ul>';
		echo '<li><a href="webadmin/managepictures.php">Manage pictures</a>';
		echo '<li><a href="link_up">Link-up</a>';
		echo '<li><a href="sunday_messages">Sunday Messages</a>';
	echo '</ul>';
	
	echo '<h4>Other pages</h4>';
	echo '<ul>';
		echo '<li><a href="whos_who">Who\'s who</a>';
		echo '<li><a href="webadmin/calendar.php">Church Activities</a>';
		echo '<li><a href="webadmin/contact_form.php">Contact Form</a>';
		echo '<li><a href="administration?tool=services">Services</a>';
	echo '</ul>';
	
	echo '<h4>Administration</h4>';
	echo '<ul>';
		echo '<li><a href="webadmin/users.php">Manage users</a>';
				
		$query = "SELECT COUNT(*) FROM users WHERE confirmed = 0";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		$row = mysql_fetch_row($result);
		$new_users = $row[0];
		
		if ($new_users > 0) {
			echo ' ('.$new_users.' new users)';
		}
		echo '</li>';
//		echo '<li><a href="webadmin/hits.php">Hits</a></li>';
	echo '</ul>';
}

function createCaptcha () {

    $chars = "abcdefghijkmnpqrstuvwxyz23456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;

}

function createPassword () {

    $chars = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $password = '' ;

    while ($i <= 5) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $password = $password . $tmp;
        $i++;
    }

    return $password;

}

function confirmMessage($message) {
	$divid = mt_rand(0,9999999999);
	$output  = '<div class="confirm" id="message'.$divid.'"><img class="cross" src="images/layout/cross_green.jpg" title="close" ';
	$output .= 'onclick="javascript:opacity(\'message'.$divid.'\', 100, 10, 500);"';
	$output .= ' /><p>'.$message.'</p></div>';
	
	return $output;
}

function getBlogSubscriptions () {
	global $user_id;
	
	$query = "SELECT blog_id as subscribedBlogId FROM blog_subscriptions WHERE user_id = '$user_id'";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	$subscribedBlogs = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result))
			array_push($subscribedBlogs, $row->subscribedBlogId);
	}
		
	return $subscribedBlogs;

}

function showBlogSubscriptionOption($blog_id) {
	global $user_id;
	$subscribedBlogs = getBlogSubscriptions();
	
	$output = '<input type="checkbox" name="subscribe'.$row->blog_id.'" id="subscribe'.$blog_id.'"';
	if (in_array($blog_id, $subscribedBlogs))
		$output .= ' checked';
	$output .= ' onClick="updateBlogSubscription('.$blog_id.', '.$user_id.')" />';
	$output .= 'Email me when this blog is updated';
	$output .= '<div id="subscriptionStatus'.$blog_id.'">';
		$output .= '<noscript>';
			$output .= '<div class="warning">';
			$output .= 'Note: You need to have JavaScript enabled to use this option. ';
			$output .= 'Please enable JavaScript and try again.</div>';
		$output .= '</noscript>';
	$output .= '</div>';
	
	echo $output;
}

function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

?>
