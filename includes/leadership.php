<?php 

  global $page;
  global $role;

	$cat = $_GET['category'];
	$id = $_GET['id'];
	$new_position = $_GET['position'];
		if ($new_position == NULL) $new_position = '1';
	$action = $_GET['action'];
	$confirm = $_GET['confirm'];
	
	if ($action == 'delete') {
		if ($confirm == 'true') {
			
			$query  = "SELECT leadership.name, shortname, leadership.priority FROM leadership, leadership_categories ";
			$query .= "WHERE leader_id = '$id' AND position_type = leadership_category_id";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$name = $row->name;
				$cat = $row->shortname;
				$priority = $row->priority;
			}
			
			$query  = "UPDATE leadership SET deleted='1', priority='0' WHERE leader_id = '$id'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
			// decrement all priorities above the deleted person so that the up and down buttons will work properly
			$query  = "UPDATE leadership SET priority=priority-1 WHERE priority > '$priority'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
			$action = NULL;
			$confirm = NULL;
			$id = NULL;
			$message = 'deleted';
		}
	}
	elseif ($action == 'movedown') {
		$query  = "SELECT leadership.priority, position_type, shortname FROM leadership, leadership_categories ";
		$query .= "WHERE leader_id = '$id' AND position_type = leadership_category_id";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$priority = $row->priority;
			$category = $row->position_type;
			$cat = $row->shortname;
		}
		$query  = "UPDATE leadership SET priority = priority-1 WHERE priority = '$priority'+1 AND position_type = '$category'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		$query  = "UPDATE leadership SET priority = priority+1 WHERE leader_id = '$id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		$id = NULL;
	}
	elseif ($action == 'moveup') {
		$query  = "SELECT leadership.priority, position_type, shortname FROM leadership, leadership_categories ";
		$query .= "WHERE leader_id = '$id' AND position_type = leadership_category_id";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$priority = $row->priority;
			$category = $row->position_type;
			$cat = $row->shortname;
		}
		$query  = "UPDATE leadership SET priority = priority+1 WHERE priority = '$priority'-1 AND position_type = '$category'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		$query  = "UPDATE leadership SET priority = priority-1 WHERE leader_id = '$id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		$id = NULL;
	}
	
	if ($id <> NULL) {
		if ($_POST['submit']) {
			$name 		   = stripslashes($_POST['title']);
			$intro 		   = stripslashes($_POST['intro']);
			$roles 		   = stripslashes($_POST['roles']);
			$bio 		   = $_POST['bio'];
			$position_type     = $_POST['position_type'];
			$position_name     = stripslashes($_POST['position_name']);
			$image 		   = $_FILES['image'];
				foreach ($image as $keyy => $valuee) { if ($keyy == 'error' && $valuee == '4') { $image = NULL; } }
			$remove		   = ($_POST['remove']);
				if ($remove == 'on') { $remove = 1; $image = NULL; } else { $remove = 0; }
				
			if ($image == NULL) {
				$query  = "SELECT image FROM leadership WHERE leader_id = '$id'";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				while($row = mysql_fetch_object($result)) {
					if ($remove == 0) {
						$image      = $row->image;
						$photo_name = $row->image;
					}
				}
			}
			else {
				$photo_types = array(
					'image/pjpeg' => 'jpg',
					'image/jpeg' => 'jpg',
				);
				$photo_name = strtolower(preg_replace("/ /", "_", $name));
				$allowed = "/[^a-z\\_]/i";
				$photo_name = preg_replace($allowed,"",$photo_name);
				
				$size  = $image['size'];
				$type  = $image['type'];
				
				foreach ($photo_types as $known_type => $known_extension) {
					if ($type == $known_type) {
						$extension = $known_extension;
					}
				}
				
				if(!array_key_exists($image['type'], $photo_types)) {
						$errors = 1;
						$error4 = 1;
						$image = NULL;
#						echo 'must be jpeg, go get a jpeg<br />';
				}
				else {
					$photo_location = "images/leadership/" . $photo_name . "." . $extension;
					if (!move_uploaded_file($image['tmp_name'], $photo_location) == 1) {
						$errors = 1;
						$error5 = 1;
						$image = NULL;
#						echo 'problem copying file';
					}
					else {
						$image = $photo_name;
					}
				}
			}
			
			if ($name == NULL) {
				$errors = 1;
				$error1 = 1;
			}
			if ($position_name == NULL) {
				$errors = 1;
				$error2 = 1;
			}
			if ($intro == NULL) {
				$errors = 1;
				$error3 = 1;
			}
			
			if (!$errors == 1) {
				if (!$id == 0) {
					$query  = "UPDATE leadership SET name='$name', intro='$intro', role='$roles', bio='$bio', ";
					if ($image <> NULL)
						$query .= "image='$photo_name', ";
					if ($remove == 1)
						$query .= "image=NULL, ";
					$query .= "position_type='$position_type', position_name='$position_name' WHERE leader_id = '$id'";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$message = 'saved';
				}
				else {
					// find priority of last person in category. increase by one to get prioirty for person being created
					$query  = "SELECT MAX(priority) FROM leadership ";
					$query .= "WHERE position_type = '$position_type' AND DELETED = '0'";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					$new_priority = mysql_fetch_row($result);
					$new_priority = $new_priority[0]+1;
										
					$query  = "INSERT INTO leadership ";
					$query .= "(name, intro, role, bio, position_type, position_name, image, priority) ";
					$query .= "VALUES ('$name', '$intro', '$roles', '$bio', ";
					$query .= "'$position_type', '$position_name', '$photo_name', '$new_priority')";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$message = 'added';
				}
			}
			else
				$action = 'edit';
		}
		else {
			$query  = "SELECT name, intro, role, bio, position_type, position_name, image FROM leadership ";
			$query .= "WHERE leader_id = '$id'";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$name  	       = $row->name;
				$intro 	       = $row->intro;
				$roles 	       = $row->role;
				$bio 	       = $row->bio;
				$position_type = $row->position_type;
				$position_name = $row->position_name;
				$image 	       = $row->image;
			}
		}
		
		if ($position_type == NULL)
			$position_type = $new_position;
		
		$query = "SELECT shortname FROM leadership_categories WHERE priority = '$position_type'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		$position_link = mysql_fetch_row($result);
		$position_link = $position_link[0];
			
		echo '<div class="iconlink"><a href="'.$PHP_SELF.'?category='.$position_link.'">';
		echo '<img title="go back" alt="go back" src="images/icons/back.png" border="0" /></a>';
		echo '<div class="text"><a href="'.$PHP_SELF.'?category='.$position_link.'">Go back</a></div></div>';
		echo '<br/><br/><br/>';
		
		if (!$message == NULL) {
			echo '<div class="confirm"><p>';
			if ($message == 'saved')
				echo 'Your changes have been saved.';
			if ($message == 'added')
				echo $name . ' has been added.';
			echo '</p></div><br />';
		}
		if ($action == 'delete') {
			echo '<div class="warning">';
			echo 'Are you sure you want to remove this person from the site? ';
			echo '<a href="'.$PHP_SELF.'?id='.$id.'&action=delete&confirm=true">Delete ';
			echo 'person</a> | <a href="'.$page.'?category='.$position_link.'">Cancel</a></div>';
		}
		elseif ($action == 'edit') {
			echo '<form enctype="multipart/form-data" name="editleader" method="post" ';
			echo 'action="'.$page.'?id='.$id.'" />';
			echo '<h4>Edit details</h4><p><span class="small">Edit the details as necessary below and then click <i>Save changes</i>.
			 	 To exit without saving, click <i>Discard changes</i>.<br />Fields displayed in <b>bold</b> are required.</span></p>';
				 
			if (!$errors == NULL) {
				echo '<div class="error"><p>There was a problem with the information you provided. ';
				echo 'Please check the sections highlighted below.</p></div>';
			}
			
			echo '<div style="width:215px; float:right">';
		}
		
		if ($image == NULL)
			echo '<div class="float_right"><img src="images/leadership/anon.jpg?rand='.rand(1,1000).'" alt="no image">';
		else
			echo '<div class="float_right"><img src="images/leadership/'.$image.'.jpg?rand='.rand(1,1000).'" alt="'.$name.', '.$position_name.'">';
		if ($action == 'edit') {
			if (!$image == NULL)
				echo '<br><input type="checkbox" name="remove" />Remove picture';
			echo '<div class="fileinputs">';
				echo '<input type="file" class="file" name="image" size="15" id="real" onchange="copyToFake()" />';
				echo '<div class="fakefile">';
					echo '<div style="position:relative; top:5px;">';
					if ($image == NULL)
						echo 'Add ';
					else
						echo 'Change ';
					echo 'picture:</div>';
					echo '<input size="21" id="fake" /> <img src="images/icons/addpicture.png" title="select picture" /><br />';
			echo '</div></div>';
		}
		echo '</div>';
		if ($action == 'edit') {
			echo '</div><div style="width:465px; float:left">';
			echo '<div class="label"><b>Name:</b></div> <input name="title" maxlength="60" size="40" value="'.$name.'"'; 
			if ($error1 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br class="clear" />';
			echo '<div class="label"><b>Position:</b></div> <input name="position_name" maxlength="60" size="40" value="'.$position_name.'"'; 
			if ($error2 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' /><br class="clear" />';
			$query  = "SELECT leadership_category_id, name FROM leadership_categories WHERE deleted = '0' ";
			$query .= "ORDER BY priority ASC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo '<div class="label"><b>Group:</b></div> <select name="position_type">';
				while($row = mysql_fetch_object($result)) {
					echo '<option value="'.$row->leadership_category_id.'"';
					if ($position_type == $row->leadership_category_id)
						echo ' SELECTED';
					echo '>'.$row->name.'</option>';
				}
				echo '</select><br class="clear" /><br class="clear" />';
			}
		}
		else {
			echo '<div class="big">'.$name.'</div>';
			echo '<p><b>'.$position_name.'</b></p>';
		}
		if ($action == 'edit') {
			echo '<div class="label"><b>Intro text:</b></div>';
			echo '<textarea name="intro" cols="50" rows="3"'; 
			if ($error3 == 1) { echo ' style="background-color:#FFAFA4"'; } echo ' >'.$intro.'</textarea><br class="clear" />';
		}
		else
			echo '<p>' . $intro . '</p>';
		if ($action == 'edit') {
			echo '<div class="label">Roles:</div>';
			echo '<textarea name="roles" cols="50" rows="3">'.$roles.'</textarea><br class="clear" />';
			echo '<div class="label"></div>';
			echo '<font size="1"><b>Tip:</b> Use commas to separate each bullet point</font><br /><br />';
			echo '</div><br class="clear" />';
		}
		else {
			if (! $roles == NULL) {
				echo '<ul class="squashed_list">';
				$roles = explode(", ", $roles);
				foreach ($roles as $li) {
					echo '<li>'.$li.'</li>';
				}
				echo '</ul>';
			}
		}
		if ($action == 'edit') {
      // show CKEditor with content
			$CKEditor = new CKEditor();
			$CKEditor->basePath = '/includes/ckeditor/';
			$CKEditor->editor('bio', stripslashes($bio));
		}
		else {
			if ($bio <> NULL)
				echo $bio;
		}
		if ($action == 'edit') {
			echo '<br />';
			echo '<input type="submit" name="submit" value="Save changes" /> ';
			if ($id == 0)
				echo '<a class="button" href="'.$page.'" />Cancel</a> ';
			else
				echo '<a class="button" href="'.$page.'?&category='.$position_link.'" />Discard changes</a> ';
			'</form>';
		}
	}	
	else {
		if ($cat == NULL) {
			$cat = 'church_staff';
		}
		
		echo '<p>Select a tab below to view the Church Staff, Elders or Deacons.<br /><br /></p>';
		
		if (!$message == NULL) {
			echo '<div class="confirm"><p>';
			if ($message == 'deleted')
				echo $name . ' has been deleted.';	
			echo '</p></div><br />';
		}
		$query  = "SELECT leadership_category_id, name, shortname FROM leadership_categories ";
		$query .= "WHERE deleted= '0' ORDER BY priority ASC";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		while($row = mysql_fetch_object($result)) {
			
			if ($row->shortname == $cat) {
				echo '<div class="tab_selected"><b>';
				$position_type = $row->leadership_category_id;
				$position_name = $row->name;
			}
			else {
				echo '<div class="tab_normal"><a href="'.$PHP_SELF.'?category='.$row->shortname.'">';
			}
			
			echo $row->name;
			
			if ($row->shortname == $cat) {
				echo '</b> ';
			}
			else {
				echo '</a> ';
			}
			
			echo '</div>';
			
		}
		
		echo '<div class="tab">';

		$query  = "SELECT leader_id, name, bio, position_name, intro, role, image, priority FROM leadership ";
		$query .= "WHERE priority > 0 AND position_type = '$position_type' AND deleted='0' ORDER BY priority ASC";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		$total = mysql_num_rows($result);
		while($row = mysql_fetch_object($result)) {
			$count++;
			echo '<div class="block">';
			if ($row->image == NULL) {
				echo '<div class="float_right"><img src="images/leadership/anon.jpg" alt="no image"></div>';
			}
			else {
				echo '<div class="float_right">';
				echo '<img src="images/leadership/' . $row->image . '.jpg" alt="' . $row->name . ', ' . $row->position_name . '">';
				echo '</div>';
			}
			echo '<div class="big">'.$row->name;
	
			// if the user has permission, show buttons for editing/deleting this person
			if ($role == 1) {
				echo ' <a class="img_button" href="'.$PHP_SELF.'?id='.$row->leader_id.'&action=edit">';
				echo '<img src="images/icons/user_edit.png" title="edit person" alt="edit person" border="0" />';
				echo '</a> ';
				echo '<a class="img_button" href="'.$PHP_SELF.'?id='.$row->leader_id.'&action=delete">';
				echo '<img src="images/icons/user_delete.png" title="delete person" alt="delete person" border="0" />';
				echo '</a> ';
				if ($count == '1')
					echo '<img src="images/icons/up_disabled.png" title="move up" alt="move up" border="0" /> ';
				else {
					echo '<a class="img_button" href="'.$PHP_SELF.'?id='.$row->leader_id.'&action=moveup">';
					echo '<img src="images/icons/up.png" title="move up" alt="move up" border="0" />';
					echo '</a> ';
				}
				if ($count == $total)
					echo '<img src="images/icons/down_disabled.png" title="move down" alt="move down" border="0" /> ';
				else {
					echo '<a class="img_button" href="'.$PHP_SELF.'?id='.$row->leader_id.'&action=movedown">';
					echo '<img src="images/icons/down.png" title="move down" alt="move down" border="0" />';
					echo '</a>';
				}
			}
			echo '</div>';
			
			echo'<p><b>'.$row->position_name.'</b></p>';
			echo '<p>' . $row->intro . '</p>';
			if (! $row->role == NULL) {
				echo '<ul class="squashed_list">';
				$roles = explode(", ", $row->role);
				foreach ($roles as $li) {
					echo '<li>'.$li.'</li>';
				}
				echo '</ul>';
			}
			if ($row->bio <> NULL) {
				echo '<a href="'.$PHP_SELF.'?id='.$row->leader_id.'">Read ';
				echo substr($row->name,0,(strpos($row->name,' ')));
				echo '\'s biography</a>';
			}
			echo '</div>';
	
		}
		// if the user has permission, show button for adding a new person
		if ($role == 1) {
			echo '<a class="button" style="height:80px;" href="'.$PHP_SELF.'?id=0&action=edit&position='.$position_type.'">';
			echo 'Add person to '.$position_name.'</a>';
		}
		echo '</div>';
	}
?>
