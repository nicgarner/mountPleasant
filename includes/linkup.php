<?php 

#	global $page;
	global $role;
	
	$id      = $_GET['article'];
	$lid     = $_GET['edition'];
	$action  = $_GET['action'];
	$confirm = $_GET['confirm'];
	
	if ($action == 'delete') {
		if ($confirm == 'true') {
			$query  = "UPDATE linkup SET deleted='1' WHERE article_id = '$id'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
			$query = "SELECT category, date FROM linkup WHERE article_id='$id'";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$category = $row->category;
				$lid = substr($row->date,5,2).'-'.substr($row->date,0,4);
			}
			
			$query  = "UPDATE linkup_categories SET articles = (articles-1) WHERE linkup_category_id = '$category'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
			$action = NULL;
			$confirm = NULL;
			$id = NULL;
			$message = 'deleted';
		}
	}
	
	
	
	if (!$id == NULL) {
		$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year, deleted FROM linkup ";
		$query .= "WHERE article_id = $id AND deleted = '0'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_object($result)) {
				$month = $row->month;
				$year = $row->year;
			}
		}
		else
			$id = NULL;
			
	}
	elseif (!$lid == NULL) {
		$date = explode('-', $lid);
		$month = $date[0];
		$year = $date[1];
	}
	
	// if after both of the above cases month is NULL, get month for latest linkup
	if ($month == NULL) {
		$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year FROM linkup ";
		$query .= "WHERE deleted='0' ORDER BY date DESC LIMIT 0, 1";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$month = $row->month;
			$year = $row->year;
		}
	}
	
	$date = $year.'-'.$month.'-'.'01';
	$month2 = $month;

	if ($_POST['submit']) {
#		$title    = addslashes($_POST['title']);
#		$story    = addslashes($_POST['story']);
#		$author   = addslashes($_POST['author']);
		$title    = ($_POST['title']);
		$story    = ($_POST['story']);
		$author   = ($_POST['author']);
		$edition  = $_POST['edition'];
		$category = $_POST['category'];
		$private  = $_POST['private'];
		if ($private == 'on')
			$private = 1;
		else
			$private = 0;

		if ($title == NULL) {
			$errors = 1;
			$error1 = 1;
		}
		if ($story == NULL) {
			$errors = 1;
			$error2 = 1;
		}
		
		if (!$errors == 1) {
			if (!$id == 0) {
				$query  = "UPDATE linkup SET title='$title', content='$story', author='$author', category='$category', ";
				$query .= "private='$private', date='$edition' WHERE article_id = '$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$message = 'saved';
			}
			else {
				$query  = "INSERT INTO linkup (title, content, author, category, date, private) ";
				$query .= "VALUES ('$title', '$story', '$author', '$category', '$edition', '$private')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$id = mysql_insert_id();
				$message = 'added';
				
				$date  = explode('-', $edition);
				$year  = $date[0];
				$month = $date[1];
				$date = $year.'-'.$month.'-'.'01';
				
				$query  = "UPDATE linkup_categories SET articles = (articles+1) WHERE linkup_category_id = '$category'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			}
		}
		else
			$action = 'edit';
	}

	$query  = "SELECT linkup.article_id, linkup.title, DATE_FORMAT(linkup.date, '%M') as month, ";
	$query .= "DATE_FORMAT(linkup.date, '%Y') as year, linkup.category, linkup_categories.name FROM linkup, linkup_categories ";
	$query .= "WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.date='$date' AND deleted='0' ";
	if (!isset($_COOKIE["cookie:mpbcadmin"])) {
		$query .= " AND linkup.private = '0' ";
	}
	$query .= "ORDER BY linkup_categories.priority ASC, linkup_categories.name ASC";
	
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_object($result)) {
			if ($id == NULL) {
				$id = $row->article_id;
			}
			$month = $row->month;
			$year = $row->year;
		}
	}
		
	echo '<div class="linkup_menu">';
	echo '<div class="section">';
	
	echo '<div class="title"><h4>'.$month.' '.$year.'</h4>';
	echo '<div class="print"><a href="print.php?edition='.$month2.'-'.$year.'" title="printer friendly version (whole magazine)">';
	echo '<img src="images/icons/print.png" class="img_button" border="0" /></a></div></div>';
	
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	
	$pso = 0;
	
	while($row = mysql_fetch_object($result)) {
		if ($row->article_id == $id) {
	/*		if ($row->category == 3) {
				if ($pso == 0) {
					echo '<b>Serving Overseas:</b><br />';
					$pso++;
				}
			}
	*/		echo '<div class="linkup_menu_on">';
				if ($row->category == 2 || $row->category == 11 || $row->category == 3) {
					echo $row->title;
				}
				else {
					echo $row->name;
				}
			echo '</div>';
		}
		else {
	/*			if ($row->category == 3) {
					if ($pso == 0) {
						echo '<b>Serving Overseas:</b><br />';
						$pso++;
					}
				}
	*/			echo '<div class="linkup_menu_off">';
				echo '<a href="'.$PHP_SELF.'?article='.$row->article_id.'">';
					if ($row->category == 2 || $row->category == 11 || $row->category == 3) {
						echo $row->title;
					}
					else {
						echo $row->name;
					}
					echo '</a>';
			echo '</div>';
		}
	}
	echo '</div>';
	if (!isset($_COOKIE["cookie:mpbcadmin"]))
		echo '<div class="message"><a href="login?target='.urlencode($_SERVER['REQUEST_URI']).'">
		      Church members can register or log in to get access to more articles.</a></div>';
	echo '<div class="section">';
	echo '<h4>Link-Up Archives</h4>';
	
	$query  = "SELECT DISTINCT DATE_FORMAT(date, '%M') as month, DATE_FORMAT(date, '%Y') as year, DATE_FORMAT(date, '%m-%Y') as link ";
	$query .= "FROM linkup WHERE deleted='0' ORDER BY date DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	$x = 0;
	while($row = mysql_fetch_object($result)) {
		if ($row->month.$row->year == $month.$year)
			echo '<div class="linkup_menu_on">';
		else
			echo '<div class="linkup_menu_off">';
		echo '<a href="'.$PHP_SELF.'?edition='.$row->link.'">'.$row->month.' '.$row->year.'</a></div>';
		$x++;
		if ($x == 3) {
			echo '<div class="link" id="morelink">';
				echo '<a href="javascript:" onclick="document.getElementById(\'more\').style.display=\'block\';document.getElementById(\'morelink\').style.display=\'none\';">Show more</a>';
			echo '</div>';
			echo '<div class="more" id="more">';
		}
	}
	echo '<div class="link" id="lesslink">';
		echo '<a href="javascript:" onclick="document.getElementById(\'more\').style.display=\'none\';document.getElementById(\'morelink\').style.display=\'block\';">Show less</a>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	if (!$_POST['submit']) {
		$query  = "SELECT title, content, author, category, private, date ";
    $query .= "FROM linkup WHERE article_id='$id'";
		if (!isset($_COOKIE["cookie:mpbcadmin"])) {
      $query .= " AND private = '0' ";
    }
    $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$title    = $row->title;
			$story    = $row->content;
			$author   = $row->author;
			$category = $row->category;
			$private  = $row->private;
			$edition  = $row->date;
		}
	}
	
	echo '<div class="linkup_page">';
		if ($role == 1 && $action == NULL) {
			echo '<div class="left" style="width:85px;"><b>Admin tools: </b></div>';
			echo '<a class="img_button" href="'.$PHP_SELF.'?article='.$id.'&action=edit">';
				echo '<img src="images/icons/edit.png" title="edit article" alt="edit article" border="0" /></a> ';
			echo '<a class="img_button" href="'.$PHP_SELF.'?article='.$id.'&action=delete">';
				echo '<img src="images/icons/deletepage.png" title="delete article" alt="delete article" border="0" /></a> ';
			echo '<a class="img_button" href="'.$PHP_SELF.'?&article=0&edition='.$month2.'-'.$year.'&action=edit">';
				echo '<img src="images/icons/addpage.png" title="add article" alt="add article" border="0" /></a><br /><br /> ';
		}
		
		if (!$message == NULL) {
			echo '<div class="confirm"><p>';
			if ($message == 'saved')
				echo 'Your changes have been saved.';
			if ($message == 'added')
				echo 'Your article has been added.';
			if ($message == 'deleted')
				echo 'The article has been deleted.';	
			echo '</p></div><br />';
		}
		
		if ($action == 'delete') {
			echo '<div class="warning">';
			echo '<p>Are you sure you want to remove this article from the magazine?</p><p align="center">';
			echo '<a href="'.$PHP_SELF.'?article='.$id.'&action=delete&confirm=true">Delete ';
			echo 'article</a> | <a href="'.$PHP_SELF.'?article='.$id.'">Cancel</a></p></div>';
		}
		
		if ($action == 'edit') {
			echo '<h3>';
			if ($id == 0)
				echo 'Add article';
			else
				echo 'Edit article';
			echo '</h3>';
			echo '<form name="editpage" method="post" action="'.$PHP_SELF.'?article='.$id.'" />';
		}
	
		if ($action == 'edit') {
			if ($error1 == 1 && $error2 == 1)
				echo '<div class="error"><p>You must enter a title and some content for the article before saving.</p></div>';
			elseif ($error1 == 1)
				echo '<div class="error"><p>You must enter a title for the article before saving.</p></div>';
			elseif ($error2 == 1)
				echo '<div class="error"><p>You must enter some content for the article before saving.</p></div>';
			echo '<div class="label">Title:</div> <input name="title" maxlength="60" size="40" value="'.$title.'" /><br class="clear" />';
		}
		else {
			if ($title == "") {
        echo '<h3>Article not found</h3>';
        echo '<p>Whoops, something\'s gone wrong - the link you\'ve followed doesn\'t seem to be available. You might need to <a href="login">log in</a> to read it. Use the links on the left to read more articles.</p>';
      }
      else {
        echo '<h3>'.stripslashes($title).'</h3>';
			
        echo '<div class="options"><div class="print"><a href="print.php?article='.$id.'" ';
        echo 'title="printer friendly version (this article)">';
        echo '<img src="images/icons/print.jpg" class="img_button" border="0" /></a></div>';
        
        // This code produces a link to the previous page, the title of the current page and a link to the next page, as an alternative to displaying the whole contents on the left hand side.
        $query  = "SELECT linkup.article_id, linkup.title, DATE_FORMAT(linkup.date,'%M') as month, ";
        $query .= "DATE_FORMAT(linkup.date,'%Y') as year, linkup.category, linkup_categories.name FROM linkup, linkup_categories ";
        $query .= "WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.date = '$date' AND deleted = 0 ";
        if (!isset($_COOKIE["cookie:mpbcadmin"])) {
          $query .= "AND linkup.private = '0' ";
        }
        $query .= "ORDER BY linkup_categories.priority ASC";
        $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
        
        $n = 0;
        
        while($row = mysql_fetch_object($result)) {
          $n++;
          $month = $row->month;
          $year = $row->year;
          if ($id == $row->article_id) {
            $a = $n;
          }
        }
        
        $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
      
        $n = 1;
      
        if (mysql_num_rows($result) > 0) {	
          while($row = mysql_fetch_object($result)) {		
            if ($n == $a-1) {
              if ($row->category == 2 || $row->category == 11 || $row->category == 3) {
                echo '<div class="selected"><a href="'.$PHP_SELF.'?article='.$row->article_id.'" ';
                echo 'title="Previous page - '.$row->title.'">';
                echo '<img src="images/layout/previous2.jpg" class="img_button" border="0" /></a></div>';
              }
              else {
                echo '<div class="selected"><a href="'.$PHP_SELF.'?article='.$row->article_id.'" ';
                echo 'title="Previous page - '.$row->name.'">';
                echo '<img src="images/layout/previous2.jpg" class="img_button" border="0" /></a></div>';
              }
              $back = 1;
            }
            elseif ($n == $a+1) {
              if ($row->category == 2 || $row->category == 11 || $row->category == 3) {
                if ($back == NULL) {
                  echo '<div class="selected">';
                  echo '<img title="You are at the first page." src="images/layout/previous2_disabled.jpg" ';
                  echo 'border="0" /></div>';
                }
                echo '<div class="selected"><a href="'.$PHP_SELF.'?article='.$row->article_id.'" ';
                echo 'title="Next page - '.$row->title.'">';
                echo '<img class="img_button" src="images/layout/next2.jpg" class="img_button" border="0" /></a></div>';
              }
              else {
                echo '<div class="selected"><a href="'.$PHP_SELF.'?article='.$row->article_id.'" ';
                echo 'title="Next page - '.$row->name.'">';
                echo '<img class="img_button" src="images/layout/next2.jpg" border="0" /></a></div>';
              }
              $forward = 1;
            }
            $n++;
          }
          if ($forward == NULL) {
            echo '<div class="selected">';
            echo '<img title="You are at the last page." src="images/layout/next2_disabled.jpg" ';
            echo 'border="0" /></div>';
          }
        } echo '</div>';
      }
    }
			
		if ($action == 'edit')
			echo '<div class="label">Author:</div> <input name="author" maxlength="60" size="40" value="'.$author.'" /><br class="clear" />';
		elseif ($author <> NULL)
			echo '<p>From ' . stripslashes($author) . '</p>';
		if ($action == 'edit') {
			echo '<div class="label">Edition:</div> <select name="edition">';
			
			//add options for next month...
			$now = mktime(0, 0, 0, date("m")+1, 1, date("Y"));
			echo '<option value="'. date("Y", $now) . '-' . date("m", $now). '-01">';
			echo date("F", $now) . ' ' . date("Y", $now) . '</option>';
			
			//...and all existing months
			$query = "SELECT MIN(date) as minDate FROM linkup WHERE deleted = '0'";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_object($result))
					$minDate = mktime(0,0,0,substr($row->minDate,6,2),1,substr($row->minDate,0,4));
			}
			else {
				$minDate = mktime(0,0,0,date("m"),1, date("Y"));
			}
			while($now > $minDate) {
				$now = mktime(0, 0, 0, date("m", $now)-1, 1, date("Y", $now));
				echo '<option value="'.date("Y", $now) . '-' . date("m", $now). '-01"';
				if ($edition == date("Y", $now) . '-' . date("m", $now). '-01')
					echo ' SELECTED';
				elseif ($lid == date("m", $now).'-'.date("Y", $now))
					echo ' SELECTED';
				echo '>'.date("F", $now).' '.date("Y", $now).'</option>';
			}
			echo '</select><br class="clear" />';
			
			$query = "SELECT linkup_category_id, name FROM linkup_categories ORDER BY priority ASC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			// check if records were returned
			if (mysql_num_rows($result) > 0) {
				echo '<div class="label">Category:</div> <select name="category">';
				while($row = mysql_fetch_object($result)) {
					echo '<option value="' . $row->linkup_category_id . '"';
						if ($category == $row->linkup_category_id) {
							echo ' SELECTED';
						}
					echo '>' . $row->name . '</option>';
				}
				echo '</select><br class="clear" />';
			}
			else
				echo 'There are no categories in the database!<br />';
				echo '<div class="label">Private?</div> <input type="checkbox" name="private" ';
					if ($private == 1) {
						echo ' checked="CHECKED" ';
					}
					echo '/><br class="clear" />';
		}
		
		if ($action == 'edit') {
			// show CKEditor with content
			$CKEditor = new CKEditor();
			$CKEditor->basePath = '/includes/ckeditor/';
			$CKEditor->editor('story', stripslashes($story));
		}
		else
			echo '<p>' . stripslashes($story) . '</p>';
		if ($action == 'edit') {
			echo '<br />';
			echo '<input type="submit" name="submit" value="Save changes" /> ';
			if ($id == 0)
				echo '<a class="button" href="'.$PHP_SELF.'" />Cancel</a> ';
			else
				echo '<a class="button" href="'.$PHP_SELF.'?article='.$id.'" />Discard changes</a> ';
			'</form>';
		}
	echo '</div>';
?>
