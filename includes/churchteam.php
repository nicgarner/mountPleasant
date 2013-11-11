<?php 

global $page;
global $role;
global $user_id;
global $editor;

$id      = $_GET['entry'];
$month   = $_GET['month'];
$action  = $_GET['action'];
$confirm = $_GET['confirm'];

$year = 2009;

if ($action == 'delete') {
	if ($confirm == 'true') {
		$query  = "UPDATE team_blog_$year SET deleted='1' WHERE blog_id = '$id'";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		$action = NULL;
		$confirm = NULL;
		$id = NULL;
		$message = 'deleted';
	}
}
/*
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
*/

# $date = $year.'-'.$month.'-'.'01';

$query  = "SELECT DISTINCT DATE_FORMAT(date, '%M') as month FROM team_blog_$year ORDER BY date DESC";
$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());

echo '<div class="linkup_menu">';
	echo '<h4>Blog entries:<br/><br/></h4>';
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		while($row = mysql_fetch_object($result)) {
			echo '<div class="linkup_menu_off"><a href="'.$PHP_SELF.'?'.$page.'&month='.strtolower($row->month).'">';
			echo $row->month.'</a></div>';
		}
	echo '<br/><br/><br/><br/>';
	if ($editor == $user_id && $action == NULL) {
			echo '<h4>Administration tools: </h4><br/>';
			echo '<a class="button" href="'.$PHP_SELF.'?page='.$page.'&entry=0&action=edit">Add entry</a><br/><br/>';
			echo '<a class="button" href="'.$PHP_SELF.'?page='.$page.'&entry='.$id.'&action=edit">Edit entry</a><br/><br/>';
			echo '<a class="button" href="'.$PHP_SELF.'?page='.$page.'&entry='.$id.'&action=delete">Delete entry</a>';
		}
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
		$entry    = addslashes($_POST['entry']);
		$date     = $_POST['date'];
		$updated  = $_POST['updated'];
		$comments = $_POST['comments'];
		$now      = date('Y-m-d H:i:s');

		if ($title == NULL) {
			$errors = 1;
			$error1 = 1;
		}
		if ($entry == NULL) {
			$errors = 1;
			$error2 = 1;
		}
		
		if (!$errors == 1) {
			if (!$id == 0) {
				$query  = "UPDATE team_blog_$year SET title='$title', content='$entry', updated='$now' WHERE blog_id = '$id'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$message = 'saved';
			}
			else {
				$query  = "INSERT INTO team_blog_$year (title, content) ";
				$query .= "VALUES ('$title', '$entry')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$id = mysql_insert_id();
				$message = 'added';
			}
		}
		else
			$action = 'edit';
	}
	
	else {
	
		echo '<div class="linkup_page">';
		
		if ($id == NULL) {
			if ($month == NULL) {
				$query  = "SELECT DATE_FORMAT(date, '%M %D %Y') as date, title, content, comments ";
				$query .= "FROM team_blog_$year WHERE deleted='0' ORDER BY date DESC LIMIT 0, 5";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				while($row = mysql_fetch_object($result)) {
					echo '<p><b>' . $row->date . '</b></p>';
					echo '<h3><a href="#">' . $row->title . '</a></h3>';
					echo '<p>' . strstr($row->content, '</p>', true) . 'l</p>';
					echo '<p>Comments: '.$row->comments.'</p>';
				}
			}
			else {
				# select all entries for the month
				echo 'show this month\'s entries';
			}
		}
		else {
			$query  = "SELECT title, content, DATE_FORMAT(date, '%M %D %Y') as date, DATE_FORMAT(updated, '%M %D %Y') as updated, ";
			$query .= "comments FROM team_blog_$year WHERE blog_id='$id'";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			while($row = mysql_fetch_object($result)) {
				$title    = $row->title;
				$entry    = $row->content;
				$date     = $row->date;
				$updated  = $row->updated;
				$comments = $row->comments;
			}			
		}
	}
		

		
		if (!$message == NULL) {
			echo '<div class="confirm"><p>';
			if ($message == 'saved')
				echo 'Your changes have been saved.';
			if ($message == 'added')
				echo 'Your blog entry has been added.';
			if ($message == 'deleted')
				echo 'The blog entry has been deleted.';	
			echo '</p></div><br />';
		}
		
		if ($action == 'delete') {
			echo '<div class="warning">';
			echo '<p>Are you sure you want to remove this entry from the blog?</p><p align="center">';
			echo '<a href="'.$PHP_SELF.'?page='.$page.'&entry='.$id.'&action=delete&confirm=true">Delete ';
			echo 'blog entry</a> | <a href="'.$PHP_SELF.'?page='.$page.'&entry='.$id.'">Cancel</a></p></div>';
		}
		
		if ($action == 'edit') {
			echo '<h3>';
			if ($id == 0)
				echo 'Add blog entry';
			else
				echo 'Edit blog entry';
			echo '</h3>';
			echo '<form name="editpage" method="post" action="'. $PHP_SELF.'?page='.$page.'&entry='.$id.'" />';
		}
		
		if ($action == 'edit') {
			echo '<input name="date" value="'.$date.'" type="hidden" /> ';
			echo '<input name="updated" value="'.$updated.'" type="hidden" />';
		}
		else {
			echo '<p><b>'.$date.'</b></p>';
		}
	
		if ($action == 'edit') {
			if ($error1 == 1 && $error2 == 1)
				echo '<div class="error"><p>You must enter a title and some content for the blog entry before saving.</p></div>';
			elseif ($error1 == 1)
				echo '<div class="error"><p>You must enter a title for the blog entry before saving.</p></div>';
			elseif ($error2 == 1)
				echo '<div class="error"><p>You must enter some content for the blog entry before saving.</p></div>';
			echo '<div class="label">Title:</div> <input name="title" maxlength="60" size=55" value="'.$title.'" /><br />';
		}
		else
			echo '<h3>'.stripslashes($title).'</h3>';
			
		if ($action == 'edit') {
			$oFCKeditor = new FCKeditor('entry') ; 
			$oFCKeditor->BasePath	= 'includes/FCKeditor/';
			$oFCKeditor->ToolbarSet = 'Basic';
			$oFCKeditor->Value = stripslashes($entry);
			$oFCKeditor->Height = 500;
			$oFCKeditor->Create();
		}
		else
			echo '<p>' . stripslashes($entry) . '</p>';
		if ($action == 'edit') {
			echo '<br />';
			echo '<input type="submit" name="submit" value="Save changes" /> ';
			if ($id == 0)
				echo '<a class="button" href="'.$PHP_SELF.'?page='.$page.'" />Cancel</a> ';
			else
				echo '<a class="button" href="'.$PHP_SELF.'?page='.$page.'&entry='.$id.'" />Discard changes</a> ';
			'</form>';
		}
		
		if ($action == 'edit')
			echo '<input name="comments" value="'.$comments.'" type="hidden" /> ';
		else
			echo '<p>Comments: '.$comments.'</p>';
		
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
