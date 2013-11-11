<?php 

global $page;
global $role;

$id      = $_GET['article'];
$lid     = $_GET['edition'];
$action  = $_GET['action'];
$confirm = $_GET['confirm'];
$view    = $_GET['view'];

if ($action == 'delete') {
	if ($confirm == 'true') {
		$query  = "UPDATE linkup SET deleted='1' WHERE article_id = '$id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		$query = "SELECT category FROM linkup WHERE article_id='$id'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$category = $row->category;
		}
		
		$query  = "UPDATE linkup_categories SET articles = (articles-1) WHERE linkup_category_id = '$category'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		$action = NULL;
		$confirm = NULL;
		$id = NULL;
		$message = 'deleted';
	}
}

if ($view == categories) {
	if (!$id == NULL) {
		$query  = "SELECT category FROM linkup ";
		$query .= "WHERE article_id = $id AND deleted='0'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		while($row = mysql_fetch_object($result)) {
			$category = $row->category;
		}
		if ($category == NULL)
			$category = 1;
	}
	$query  = "SELECT linkup.title, linkup.article_id, linkup.category, linkup_categories.name FROM linkup, linkup_categories ";
	$query .= "WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.category='$category' AND deleted='0' ";
	if (!isset($_COOKIE["cookie:mpbcadmin"])) {
		$query .= " AND linkup.private = '0' ";
	}
	$query .= "ORDER BY linkup_categories.priority ASC, linkup_categories.name ASC";
}

else {
	if (!$id == NULL) {
		$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year FROM linkup ";
		$query .= "WHERE article_id = $id AND deleted='0'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		while($row = mysql_fetch_object($result)) {
			$month = $row->month;
			$year = $row->year;
		}
	}
	elseif (!$lid == NULL) {
		$date = explode('-', $lid);
		$month = $date[0];
		$year = $date[1];
	}
	else {
		$query  = "SELECT DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y') as year FROM linkup ";
		$query .= "WHERE deleted='0' ORDER BY date DESC LIMIT 0, 1";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$month = $row->month;
			$year = $row->year;
		}
	}
	$date = $year.'-'.$month.'-'.'01';
	
	$query  = "SELECT linkup.title, DATE_FORMAT(linkup.date, '%M') as month, DATE_FORMAT(linkup.date, '%Y') as year, ";
	$query .= "linkup.article_id, linkup.category, linkup_categories.name FROM linkup, linkup_categories ";
	$query .= "WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.date='$date' AND deleted='0' ";
	if (!isset($_COOKIE["cookie:mpbcadmin"])) {
		$query .= " AND linkup.private = '0' ";
	}
	$query .= "ORDER BY linkup_categories.priority ASC, linkup_categories.name ASC";

}

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

echo '<h4>Link-up '.$month.' '.$year.'</a><br><br></h4>';

$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());

$pso = 0;

while($row = mysql_fetch_object($result)) {
	if ($row->article_id == $id) {
		if ($row->category == 3) {
			if ($pso == 0) {
				echo '<b>Serving Overseas:</b><br />';
				$pso++;
			}
		}
		echo '<div class="linkup_menu_on">';
			if ($row->category == 2 || $row->category == 11 || $row->category == 3) {
				echo $row->title;
			}
			else {
				echo $row->name;
			}
		echo '</div>';
	}
	else {
			if ($row->category == 3) {
				if ($pso == 0) {
					echo '<b>Serving Overseas:</b><br />';
					$pso++;
				}
			}
			echo '<div class="linkup_menu_off">';
			echo '<a href="'.$PHP_SELF.'?'.$page.'&article='.$row->article_id.'">';
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

if (!isset($_COOKIE["cookie:mpbcadmin"])) {
	echo '<div class="message"><a href="'.$PHP_SELF.'?login&destination=link_up">Click here to register or login to get access to more articles.</a></div>';
}

echo '<br><h4>Link-Up Archives<br><br></h4>';

$query  = "SELECT DISTINCT DATE_FORMAT(date, '%M') as month, DATE_FORMAT(date, '%Y') as year, DATE_FORMAT(date, '%m-%Y') as link ";
$query .= "FROM linkup WHERE deleted='0' ORDER BY date DESC";
$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
while($row = mysql_fetch_object($result)) {
	echo '<div class="linkup_menu_off"><a href="'.$PHP_SELF.'?'.$page.'&edition='.$row->link.'">'.$row->month.' '.$row->year.'</a></div>';
}

echo '<br><h4><a href="'.$PHP_SELF.'?'.$page.'&view=categories">Browse categories</a></h4>';

echo '</div>';


/* This code produces a link to the previous page, the title of the current page and a link to the next page, as an alternative to displaying the whole contents on the left hand side.
	$query = "SELECT linkup.article_id, linkup.title, DATE_FORMAT(linkup.date, '%M') as month, DATE_FORMAT(linkup.date, '%Y') as year, linkup.category, linkup_categories.name FROM linkup, linkup_categories WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.date = '$date' ORDER BY linkup_categories.priority ASC";
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
		echo '<table width="100%" border="0">';		
		echo '<tr><td colspan="3"><h4>Link-up '.$month.' '.$year.'</h4></td></tr><tr>';	
		while($row = mysql_fetch_object($result)) {		
			if ($n == $a-1) {
				echo '<td width="33%" align="left">';
				if ($row->category == 2) {
					echo '< <a href="'.$PHP_SELF.'?'.$page.'&lid='.$row->article_id.'">'.$row->title.'</a> ';
				}
				else {
					echo '< <a href="'.$PHP_SELF.'?'.$page.'&lid='.$row->article_id.'">'.$row->name.'</a> ';
				}
				echo '</td>';
			}
			elseif ($n == $a) {
				echo '<td width="33%" align="center"><b>';
				if ($row->category == 2) {
					echo $row->title;
				}
				else {
					echo $row->name;
				}
				echo '</td></b> ';
			}
			elseif ($n == $a+1) {
				echo '<td width="33%" align="right">';
				if ($row->category == 2) {
					echo '<a href="'.$PHP_SELF.'?'.$page.'&lid='.$row->article_id.'">'.$row->title.'</a> >';
				}
				else {
					echo '<a href="'.$PHP_SELF.'?'.$page.'&lid='.$row->article_id.'">'.$row->name.'</a> >';
				}
				echo '</td>';
			}
			$n++;
		}
		echo '</tr></table>';
	} */
	
	if ($_POST['submit']) {
		$title    = addslashes($_POST['title']);
		$story    = addslashes($_POST['story']);
		$author   = addslashes($_POST['author']);
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
				$query .= "private='$private' WHERE article_id = '$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$message = 'saved';
			}
			else {
				$query  = "INSERT INTO linkup (title, content, author, category, date, private) ";
				$query .= "VALUES ('$title', '$story', '$author', '$category', '$edition', '$private')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$id = mysql_insert_id();
				$message = 'added';
				
				$query  = "UPDATE linkup_categories SET articles = (articles+1) WHERE linkup_category_id = '$category'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			}
		}
		else
			$action = 'edit';
	}
	
	else { 
		$query = "SELECT title, content, author, category, private FROM linkup WHERE article_id='$id'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			$title    = $row->title;
			$story    = $row->content;
			$author   = $row->author;
			$category = $row->category;
			$private  = $row->private;
		}
	}
		
	echo '<div class="linkup_page">';
		if ($role == 1 && $action == NULL) {
			echo '<font color="#00584c"><b>Administration tools: </b></font>';
			echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'?page='.$page.'&article='.$id.'&action=edit">Edit article</a> ';
			echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'?page='.$page.'&article='.$id.'&action=delete">Delete article</a> ';
			echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'?page='.$page.'&article=0&action=edit">Add article</a><br /><br /> ';
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
			echo '<a href="'.$_SERVER['PHP_SELF'].'?page='.$page.'&article='.$id.'&action=delete&confirm=true">Delete ';
			echo 'article</a> | <a href="'.$_SERVER['PHP_SELF'].'?page='.$page.'&article='.$id.'">Cancel</a></p></div>';
		}
		
		if ($action == 'edit') {
			echo '<h3>';
			if ($id == 0)
				echo 'Add article';
			else
				echo 'Edit article';
			echo '</h3>';
			echo '<form name="editpage" method="post" action="'. $_SERVER['PHP_SELF'].'?page='.$page.'&article='.$id.'" />';
		}
	
		if ($action == 'edit') {
			if ($error1 == 1 && $error2 == 1)
				echo '<div class="error"><p>You must enter a title and some content for the article before saving.</p></div>';
			elseif ($error1 == 1)
				echo '<div class="error"><p>You must enter a title for the article before saving.</p></div>';
			elseif ($error2 == 1)
				echo '<div class="error"><p>You must enter some content for the article before saving.</p></div>';
			echo '<div class="label">Title:</div> <input name="title" maxlength="60" size="40" value="'.$title.'" /><br />';
		}
		else
			echo '<h3>'.stripslashes($title).'</h3>';
			
		if ($action == 'edit')
			echo '<div class="label">Author:</div> <input name="author" maxlength="60" size="40" value="'.$author.'" /><br />';
		elseif ($author <> NULL)
			echo '<p>From ' . stripslashes($author) . '</p>';
		if ($action == 'edit') {
			if ($id == 0) {
				echo '<div class="label">Edition:</div> <select name="edition">';
				
				//add options for next month...
				$now = mktime(0, 0, 0, date("m")+1, 1, date("Y"));
				echo '<option value="'. date("Y", $now) . '-' . date("m", $now). '-01">';
				echo date("F", $now) . ' ' . date("Y", $now) . '</option>';
				
				//...and the previous five months
				$i = 1;
				while($i < 6) {
					$now = mktime(0, 0, 0, date("m", $now)-1, 1, date("Y", $now));
					echo '<option value="'.date("Y", $now) . '-' . date("m", $now). '-01"';
					if ($edition == date("Y", $now) . '-' . date("m", $now). '-01')
						echo ' SELECTED';
					echo '>'.date("F", $now).' '.date("Y", $now).'</option>';
					$i++;
				}
				echo '</select><br />';
			}
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
				echo '</select><br />';
			}
			else
				echo 'There are no categories in the database!<br />';
				echo '<div class="label">Private?</div> <input type="checkbox" name="private" ';
					if ($private == 1) {
						echo ' checked="CHECKED" ';
					}
					echo '/><br />';
		}
		
		if ($action == 'edit') {
			$oFCKeditor = new FCKeditor('story') ; 
			$oFCKeditor->BasePath	= 'includes/FCKeditor/';
			$oFCKeditor->ToolbarSet = 'Basic';
			$oFCKeditor->Value = stripslashes($story);
			$oFCKeditor->Height = 500;
			$oFCKeditor->Create();
		}
		else
			echo '<p>' . stripslashes($story) . '</p>';
		if ($action == 'edit') {
			echo '<br />';
			echo '<input type="submit" name="submit" value="Save changes" /> ';
			if ($id == 0)
				echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'?page='.$page.'" />Cancel</a> ';
			else
				echo '<a class="button" href="'.$_SERVER['PHP_SELF'].'?page='.$page.'&article='.$id.'" />Discard changes</a> ';
			'</form>';
		}
	echo '</div>';
	
/*
else {
	
	echo '<h3>Link-up Magazine</h3>';
	echo '<p><b>News and Views from Mount Pleasant Baptist Church</b></p>';

	echo '<p>The latest edition of Link-up is <b>'.$month_name.' '.$year.'</b>. Click here to read Link-up Select a headline below to read the article.</p>';

	$query = "SELECT linkup.article_id, linkup.title, linkup.author, linkup.date, linkup.category, linkup_categories.name FROM linkup, linkup_categories WHERE linkup.category = linkup_categories.linkup_category_id AND linkup.date = '$date' ORDER BY linkup_categories.priority ASC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());

	if (mysql_num_rows($result) > 0) {	
		echo '<ul>';
			while($row = mysql_fetch_object($result)) {		
				echo '<li><a href="'.$PHP_SELF.'?'.$page.'&article='.$row->article_id.'">';
				if ($row->category == 2) {
					echo $row->title;
				}
				else {
					echo $row->name;
				}
				echo '</a></li>';
			}
		echo '</ul>';
	}
	else {
		echo '<p>There are no articles availiable online yet for this month\'s Link-up.</p>';
	}
	echo '<p><br><br>Older editions can be read by clicking on the links below. Or, use the links on the right to view articles by category.</p>';
	
	$query = "SELECT DISTINCT DATE_FORMAT(date, '%M') as month, DATE_FORMAT(date, '%Y') as year, DATE_FORMAT(date, '%m-%y') as link FROM linkup ORDER BY date DESC";
	$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
	echo '<ul>';
	while($row = mysql_fetch_object($result)) {
		echo '<li><a href="'.$PHP_SELF.'?edition='.$page.'&edition='.$row->link.'">'.$row->month.' '.$row->year.'</a>';
	}
	echo '</ul>';
}
*/
?>
